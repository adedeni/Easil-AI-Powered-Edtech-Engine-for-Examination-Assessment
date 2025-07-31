# CEREUS-EDU
A repo that contains code for a system that sllows teachers upload documents and generate quizzes based on that, students can also take the test and be assesed by AI.



A FastAPI-powered backend application that transforms educational PDFs into interactive learning experiences.

It allows teachers to:

* Upload PDF study materials.
* Automatically generate quizzes (MCQs, short answers, and essay prompts).
* Chat with the content of the uploaded PDF.
* Submit quiz responses for automated scoring.
* Retrieve quizzes by their ID.

---

## Features

* **PDF Upload & Indexing**: Upload PDF files which are processed, chunked, and indexed using FAISS with embeddings from Google Generative AI.
* **Chat Endpoint**: Ask questions against the uploaded PDF content.
* **Quiz Generation**: Auto-generate quizzes with multiple-choice, short answer, and essay questions.
* **Quiz Submission & Grading**: Submit answers to generated quizzes for instant scoring and feedback.
* **Retrieve Quiz by ID**: Fetch the full quiz content via its unique ID.

---

## Setup Instructions

### Requirements

* Python 3.9+
* Google Generative AI API key

### Installation

```bash
# Clone this repository
git clone <repo_url>
cd <repo_dir>

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt
```

### Environment Variables

Create a `.env` file with:

```env
GOOGLE_API_KEY=your_google_api_key
```

### Running the Application

```bash
uvicorn main:app --reload
```



---

## API Endpoints

### 1. **Upload Document**

`POST /documents`

**Form Data:**

* `teacher_name`: (string) Name of the teacher.
* `subject`: (string) Subject title.
* `file`: (file) PDF file.

**Response:**

```json
{
  "status": "indexed",
  "document_id": "..."
}
```

---

### 2. **Chat with PDF**

`POST /chat`

**Payload:**

```json
{
  "document_id": "...",
  "question": "..."
}
```

**Response:**

```json
{
  "answer": "..."
}
```

---

### 3. **Generate Quiz**

`POST /quizzes`

**Payload:**

```json
{
  "document_id": "...",
  "mcq_count": 5,
  "short_count": 3,
  "essay_count": 1
}
```

**Response:**

```json
{
  "quiz_id": "...",
  "quiz": {
    "mcq": [...],
    "short": [...],
    "essay": [...]
  }
}
```

---

### 4. **Submit Quiz Answers**

`POST /quizzes/{quiz_id}/submit`

**Payload:**

```json
{
  "answers": {
    "mcq_1": "Answer",
    "short_1": "Answer text",
    "essay_1": "Essay text"
  }
}
```

**Response:**

```json
{
  "report": {
    "mcq": {...},
    "short": {...},
    "essay": {...}
  },
  "quiz_id": "..."
}
```

---

### 5. **Retrieve Quiz by ID**

`GET /quizzes/{quiz_id}`

**Response:**

```json
{
  "quiz_id": "...",
  "document_id": "...",
  "quiz": {
    "mcq": [...],
    "short": [...],
    "essay": [...]
  }
}
```

---

## Technologies Used

* **FastAPI**: API framework.
* **FAISS**: Vector indexing.
* **LangChain**: LLM orchestration.
* **Google Generative AI**: Embeddings and LLM responses.
* **Pydantic**: Data validation.
* **Uvicorn**: ASGI server.

---

## Future Enhancements

* Persistent storage for document and quiz data.
* Enhanced grading with configurable rubrics.
* User authentication & authorization.
* Web-based UI for teachers and students.

---

## License

MIT License

## Author

@abeenoch

---

For any issues, please create a GitHub issue or pull request!
