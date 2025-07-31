import os
import json
import tempfile

import streamlit as st
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

# ─── Streamlit / Env Setup ───
nest_asyncio.apply()
load_dotenv()
st.set_page_config(page_title="StudyAssist", page_icon=":book:")
st.title("StudyAssist ")
st.write("Upload a PDF, choose the types of questions you want, then generate your custom quiz.")

# ─── Cache LLM and Retriever ───
@st.cache_resource
def get_llm():
    return ChatGoogleGenerativeAI(
        model="gemini-1.5-flash-latest",
        google_api_key=os.getenv("GOOGLE_API_KEY")
    )

@st.cache_resource
def build_retriever(pdf_bytes: bytes):
    tmp = tempfile.NamedTemporaryFile(suffix=".pdf", delete=False)
    tmp.write(pdf_bytes); tmp.close()
    loader = PyPDFLoader(tmp.name, extract_images=True)
    pages = loader.load()
    splitter = RecursiveCharacterTextSplitter(
        chunk_size=1000, chunk_overlap=100, separators=["\n\n"]
    )
    docs = splitter.split_documents(pages)
    embeddings = GoogleGenerativeAIEmbeddings(model="models/embedding-001")
    vs = faiss.FAISS.from_documents(docs, embeddings)
    retriever = vs.as_retriever(search_type="mmr", search_kwargs={"k":5,"fetch_k":10})
    try: os.remove(tmp.name)
    except: pass
    return retriever

llm = get_llm()

# ─── Quiz Generation Logic ───
def generate_quiz(retriever, llm, mcq: int, short: int, essay: int):
    docs = retriever.get_relevant_documents("")[:5]
    context = "\n\n".join(d.page_content for d in docs)
    prompt = f"""
Context:
{context}

Instruction:
1) Generate {mcq} multiple‑choice questions (4 options each; clearly mark the correct answer).
2) Generate {short} short‑answer questions (include answers).
3) Generate {essay} essay question(s) (no answers needed).

Return a JSON object with keys "mcq", "short", and "essay".
"""
    resp = llm.predict(prompt)
    try:
        return json.loads(resp)
    except json.JSONDecodeError:
        st.error("⚠️ LLM returned malformed JSON:")
        st.code(resp)
        return None

# ─── PDF Upload & Retriever Build ───
uploaded = st.file_uploader("Upload your PDF", type="pdf")
if uploaded:
    unique_id = uploaded.name + str(uploaded.size)
    if st.session_state.get("retriever_id") != unique_id:
        st.session_state.retriever_id = unique_id
        with st.spinner("📚 Indexing document…"):
            st.session_state.retriever = build_retriever(uploaded.getvalue())
        st.success("✅ Document indexed!")

# ─── Quiz Configuration UI ───
if uploaded and st.session_state.get("retriever"):
    st.markdown("### Select Quiz Types & Counts")
    cols = st.columns(3)
    with cols[0]:
        want_mcq = st.checkbox("Include MCQs?", value=True)
        mcq_count = st.number_input("MCQ count", min_value=1, max_value=20, value=3) if want_mcq else 0
    with cols[1]:
        want_short = st.checkbox("Include Short‑answer?", value=True)
        short_count = st.number_input("Short‑answer count", min_value=1, max_value=20, value=2) if want_short else 0
    with cols[2]:
        want_essay = st.checkbox("Include Essay?", value=False)
        essay_count = st.number_input("Essay count", min_value=1, max_value=10, value=1) if want_essay else 0

    if st.button("📝 Generate Quiz"):
        retriever = st.session_state.retriever
        with st.spinner("Generating your custom quiz…"):
            st.session_state.quiz = generate_quiz(
                retriever, llm,
                mcq=mcq_count,
                short=short_count,
                essay=essay_count
            )

# ─── Render Generated Quiz ───
quiz = st.session_state.get("quiz")
if quiz:
    st.markdown("## 📝 Your Generated Quiz")
    if quiz.get("mcq"):
        st.markdown("### Multiple‑Choice")
        for i, itm in enumerate(quiz["mcq"], 1):
            st.markdown(f"**Q{i}. {itm['question']}**")
            st.radio("", itm["options"], key=f"mcq_{i}")
            st.caption(f"Answer: {itm['answer']}")
    if quiz.get("short"):
        st.markdown("### Short‑Answer")
        for j, itm in enumerate(quiz["short"], 1):
            st.markdown(f"**Short Q{j}. {itm['question']}**")
            st.text_input("Your answer", key=f"short_{j}")
            st.caption(f"Answer: {itm['answer']}")
    if quiz.get("essay"):
        st.markdown("### Essay")
        for k, itm in enumerate(quiz["essay"], 1):
            st.markdown(f"**Essay Q{k}. {itm['question']}**")
            st.text_area("Your response…", key=f"essay_{k}", height=150)

# ─── (Optional) Conversational Chat ───
st.markdown("---")
st.markdown("## 💬 Chat with your PDF")
if "messages" not in st.session_state:
    st.session_state.messages = [{"role":"assistant","content":"Hi there!"}]
for msg in st.session_state.messages:
    with st.chat_message(msg["role"]):
        st.markdown(msg["content"])
if chat_in := st.chat_input("Ask a question…"):
    st.session_state.messages.append({"role":"user","content":chat_in})
    if st.session_state.get("retriever"):
        mem = ConversationBufferMemory(memory_key="chat_history", return_messages=True)
        chain = ConversationalRetrievalChain.from_llm(
            llm=llm, retriever=st.session_state.retriever, memory=mem
        )
        with st.spinner("Thinking…"):
            ans = chain.run(chat_in)
    else:
        ans = "Please upload and index a PDF first."
    st.session_state.messages.append({"role":"assistant","content":ans})
    with st.chat_message("assistant"):
        st.markdown(ans)
