import os
import json
import tempfile
import uuid

from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from pydantic import BaseModel
from typing import Dict, Optional

from dotenv import load_dotenv
import nest_asyncio

from langchain_community.document_loaders import PyPDFLoader
from langchain_community.vectorstores import faiss
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_google_genai import (
    ChatGoogleGenerativeAI,
    GoogleGenerativeAIEmbeddings,
)
from langchain.memory import ConversationBufferMemory
from langchain.chains import ConversationalRetrievalChain
from PIL import Image

# Streamlit / Env Setup (ported to FastAPI) 
load_dotenv()
nest_asyncio.apply() 

# Instantiate a single LLM instance for all requests
LLM = ChatGoogleGenerativeAI(
    model="gemini-1.5-flash-latest",
    google_api_key=os.getenv("GOOGLE_API_KEY")
)

# document_id → FAISS retriever
RETRIEVER_STORE: Dict[str, faiss.FAISS] = {}

# quiz_id → { "document_id": str, "quiz": dict }
QUIZ_STORE: Dict[str, Dict] = {}
# Helper: Build FAISS Retriever from PDF bytes
from contextlib import contextmanager
from PIL import Image
from langchain_community.document_loaders import PyPDFLoader
import tempfile
import os
from langchain.text_splitter import RecursiveCharacterTextSplitter
# context manager to patch PIL's Image.fromarray
@contextmanager
def patch_fromarray():
    original_fromarray = Image.fromarray
    def patched_fromarray(arr, *args, **kwargs):
        # If the array is 3D with a single channel, squeeze it to 2D
        if arr.ndim == 3 and arr.shape[2] == 1:
            arr = arr[:, :, 0]
        return original_fromarray(arr, *args, **kwargs)
    Image.fromarray = patched_fromarray
    try:
        yield
    finally:
        # original fromarray function
        Image.fromarray = original_fromarray

def build_retriever_from_pdf(pdf_bytes: bytes) -> faiss.FAISS:
    """
    1) Write PDF bytes to a temp file so PyPDFLoader can open it.
    2) Parse pages, chunk them, embed via GoogleGenerativeAIEmbeddings, build a FAISS index.
    3) Return the retriever.
    """
    tmp = tempfile.NamedTemporaryFile(suffix=".pdf", delete=False)
    try:
        tmp.write(pdf_bytes)
        tmp.close()

        loader = PyPDFLoader(tmp.name, extract_images=True)
        # Apply patch only during loading process
        with patch_fromarray():
            pages = loader.load()

        splitter = RecursiveCharacterTextSplitter(
            chunk_size=1000,
            chunk_overlap=100,
            separators=["\n\n"]
        )
        docs = splitter.split_documents(pages)

        embeddings = GoogleGenerativeAIEmbeddings(model="models/embedding-001")
        vs = faiss.FAISS.from_documents(docs, embeddings)
        retriever = vs.as_retriever(
            search_type="mmr", search_kwargs={"k": 5, "fetch_k": 10}
        )

        return retriever
    finally:
        try:
            os.remove(tmp.name)
        except OSError:
            pass
# Helper: Single‐turn Chat via Retrieval + LLM
def chat_with_retriever(retriever: faiss.FAISS, question: str) -> str:
    """
    Uses LangChain's ConversationalRetrievalChain for a single‐turn
    retrieval + answer. We attach a fresh ConversationBufferMemory, but
    not stored between calls (no multi‐turn history persists).
    """
    memory = ConversationBufferMemory(memory_key="chat_history", return_messages=True)
    chain = ConversationalRetrievalChain.from_llm(
        llm=LLM, retriever=retriever, memory=memory, verbose=False
    )
    return chain.run(question)

# Helper: Generate Quiz JSON from Retriever 
def generate_quiz_from_retriever(
    retriever: faiss.FAISS,
    mcq: int,
    short: int,
    essay: int
) -> Optional[Dict]:
    """
    1) Retrieve top‐5 chunks as context.
    2) Prompt LLM to produce JSON with keys "mcq", "short", "essay".
    """
    docs = retriever.get_relevant_documents("")[:5]
    context = "\n\n".join(d.page_content for d in docs)

    prompt = f"""
Context:
{context}

Instruction:
1) Generate {mcq} multiple-choice questions (4 options each; clearly mark the correct answer).
2) Generate {short} short-answer questions (include answers).
3) Generate {essay} essay question(s) (no answers needed).

Return a JSON object with keys "mcq", "short", and "essay". 
The structure should look like:
{{
  "mcq": [
    {{"question": "...", "options": ["A","B","C","D"], "answer": "B"}}, …
  ],
  "short": [
    {{"question": "...", "answer": "..."}}, …
  ],
  "essay": [
    {{"question": "..."}}, …
  ]
}}
""".strip()

    resp = LLM.predict(prompt)
    try:
        return json.loads(resp)
    except json.JSONDecodeError:
        return None

# Pydantic Models for Incoming JSON 
class ChatRequest(BaseModel):
    document_id: str
    question: str

class QuizCreateRequest(BaseModel):
    document_id: str
    mcq_count: int
    short_count: int
    essay_count: int

class QuizSubmitRequest(BaseModel):
    answers : Dict[str, str]  # e.g. {"mcq_1": "A", "short_1": "answer", "essay_1": "my essay answer"}
    

#FastAPI App & Endpoints 
app = FastAPI(
    title="StudyAssist API",
    description="Upload PDF → Chat → Generate Quiz",
    version="1.0"
)

@app.post("/documents", summary="Upload and index a new PDF")
async def upload_document(
    teacher_name: str = Form(...),
    subject: str      = Form(...),
    file: UploadFile  = File(...)
):
    """
    - Accepts multipart/form-data: teacher_name, subject, file (PDF).
    - Builds a FAISS retriever in memory.
    - Returns a generated document_id.
    """
    pdf_bytes = await file.read()
    retriever = build_retriever_from_pdf(pdf_bytes)

    document_id = f"{teacher_name}-{subject}-{uuid.uuid4().hex}"
    RETRIEVER_STORE[document_id] = retriever

    return {"status": "indexed", "document_id": document_id}


@app.post("/chat", summary="Chat with an indexed PDF")
def chat_endpoint(req: ChatRequest):
    """
    - Body: { "document_id": "...", "question": "Your query" }
    - Returns: { "answer": "LLM's response" }
    """
    retriever = RETRIEVER_STORE.get(req.document_id)
    if not retriever:
        raise HTTPException(status_code=404, detail="document_id not found")

    answer = chat_with_retriever(retriever, req.question)
    return {"answer": answer}


@app.post("/quizzes", summary="Generate a quiz from an indexed PDF")
def generate_quiz_endpoint(req: QuizCreateRequest):
    retriever = RETRIEVER_STORE.get(req.document_id)
    if not retriever:
        raise HTTPException(status_code=404, detail="document_id not found")

    docs = retriever.get_relevant_documents("")[:5]
    context = "\n\n".join(d.page_content for d in docs)
    prompt = f"""
Context:
{context}

Instruction:
Generate a quiz based on the provided context with the following specifications:
- {req.mcq_count} multiple-choice questions, each with 4 options and a clearly marked correct answer.
- {req.short_count} short-answer questions, each with a concise answer.
- {req.essay_count} essay questions, without answers.

Return the quiz as a valid JSON object with the following structure:
{{
  "mcq": [
    {{"question": "Question text", "options": ["Option A", "Option B", "Option C", "Option D"], "answer": "Correct Option"}}
  ],
  "short": [
    {{"question": "Question text", "answer": "Answer text"}}
  ],
  "essay": [
    {{"question": "Question text"}}
  ]
}}

Ensure that:
- The response contains *only* the JSON object, with no additional text, explanations, or markdown formatting (e.g., do not include ```json or ```).
- All keys and string values are enclosed in double quotes.
- There are no trailing commas or syntax errors in the JSON.
""".strip()
    
    raw = LLM.predict(prompt)
    try:
        quiz_data = json.loads(raw)
    except json.JSONDecodeError:
        return {
            "error": "Malformed JSON from LLM",
            "raw_output": raw
        }

    quiz_id = uuid.uuid4().hex
    QUIZ_STORE[quiz_id] = {"document_id": req.document_id, "quiz": quiz_data}
    return {"quiz_id": quiz_id, "quiz": quiz_data}


@app.post("/quizzes/{quiz_id}/submit", summary="Submit answers for a quiz")
def submit_quiz(quiz_id: str, req: QuizSubmitRequest):
    record = QUIZ_STORE.get(quiz_id)
    if not record:
        raise HTTPException(status_code=404, detail="quiz_id not found")
    
    mcq_score = 0  # Must be here, before any usage
    essay_score = 0
    short_score = 0
    quiz = record["quiz"]
    answers = req.answers
    
    mcq_feedback = []
    for i, itm in enumerate(quiz.get("mcq",[]), start=1):
        key = f"mcq_{i}"
        selected = answers.get(key)
        correct = itm.get("answer")
        is_correct = (selected == correct)
        if is_correct:
            mcq_score += 1
        mcq_feedback.append({
            "question": itm["question"],
            "selected": selected,
            "correct": correct,
            "is_correct": is_correct
        })
    
    short_feedback = []
    total_short_score = 0
    for j, itm in enumerate(quiz.get("short", []), start=1):
        key = f"short_{j}"
        student_ans = answers.get(key, "")
        model_ans = itm["answer"]
        prompt = f"""
        You are a teacher grading a short answer question. 
        Grade the student's answer on a scale of 0-10 and provide brief feedback.
        Return your response strictly as a valid JSON object with exactly two fields: 
        "score" (an integer) and "feedback" (a string). 
        Do not include any additional text outside the JSON object.

        Question: {itm["question"]}
        Model Answer: {model_ans}
        Student Answer: {student_ans}
        """
        import re
        resp = LLM.predict(prompt)
        print(f"LLM response for short question {j}: {resp}")

        match = re.search(r'\{.*\}', resp, re.DOTALL)
        if match:
            try:
                out = json.loads(match.group(0))
            except json.JSONDecodeError:
                out = {"score": 0, "feedback": "Could not parse LLM response"}
        else:
            out = {"score": 0, "feedback": "No JSON found in LLM response"}

        total_short_score += out.get("score", 0)
        short_feedback.append({
        "question": itm["question"],
        "student_answer": student_ans,
        "score": out.get("score", 0),
        "feedback": out.get("feedback", "")
        })
        
    essay_feedback = []
    total_essay_score = 0
    for k, itm in enumerate(quiz.get("essay",[]), start=1):
        key = f"essay_{k}"
        student_ans = answers.get(key, "")
        prompt = f"""
        You are a teacher grading an essay question.
        Essay question: {itm["question"]}
        Student Response: {student_ans}
        
        Grade the essay on a scale of 0-10 and return JSON:
        {{"score": <int>, "feedback":"<comment>"}}
        """
        
        import re
        resp = LLM.predict(prompt)
        print(f"LLM response for essay question {k}: {resp}")

        match = re.search(r'\{.*\}', resp, re.DOTALL)
        if match:
            try:
                out = json.loads(match.group(0))
            except json.JSONDecodeError:
                out = {"score": 0, "feedback": "Could not parse LLM response"}
        else:
            out = {"score": 0, "feedback": "No JSON found in LLM response"}

        total_essay_score += out.get("score", 0)
        essay_feedback.append({
    "question": itm["question"],
        "student_answer": student_ans,
        "score": out.get("score", 0),
        "feedback": out.get("feedback", "")
        })


        
    report = {
        "mcq":{
            "score": mcq_score,
            "out_of": len(quiz.get("mcq", [])),
            "feedback": mcq_feedback
        },
        
        "short": {
            "score": total_short_score,
            "out_of": len(quiz.get("short", [])) * 10, 
            "feedback": short_feedback
        },
        "essay": {
            "score": total_essay_score,
            "out_of": len(quiz.get("essay", [])) * 10,
            "feedback": essay_feedback
        }
        
        
    }
    return {"report": report, "quiz_id": quiz_id}    

from fastapi import HTTPException

@app.get("/quizzes/{quiz_id}", summary="Retrieve a quiz by its ID")
def get_quiz(quiz_id: str):
    """
    - Path param: quiz_id (string)
    - Returns the saved quiz JSON for that ID, or 404 if not found.
    """
    record = QUIZ_STORE.get(quiz_id)
    if not record:
        raise HTTPException(status_code=404, detail="quiz_id not found")
    return {
        "quiz_id": quiz_id,
        "document_id": record["document_id"],
        "quiz": record["quiz"]
    }

            