<?php
// Exams API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';
$examId = isset($requestUri[3]) ? $requestUri[3] : null;

// Check authentication for all exam operations
if (!Session::exists('user_id')) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "error" => "Authentication required"
    ]);
    exit();
}

// Get current user info
$currentUser = new User();
$currentUserData = $currentUser->find(Session::get('user_id'));
$isAdmin = $currentUserData && $currentUserData->role_id == 1;
$isLecturer = $currentUserData && $currentUserData->role_id == 3;
$isStudent = $currentUserData && $currentUserData->role_id == 2;

switch ($action) {
    case '':
        if ($requestMethod === 'GET') {
            handleGetExams();
        } elseif ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'create':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'update':
        if ($requestMethod === 'PUT' && ($isAdmin || $isLecturer)) {
            handleUpdateExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'delete':
        if ($requestMethod === 'DELETE' && ($isAdmin || $isLecturer)) {
            handleDeleteExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'questions':
        if ($requestMethod === 'GET') {
            handleGetExamQuestions();
        } elseif ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleAddExamQuestion();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'take':
        if ($requestMethod === 'POST' && $isStudent) {
            handleTakeExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'submit':
        if ($requestMethod === 'POST' && $isStudent) {
            handleSubmitExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'results':
        if ($requestMethod === 'GET') {
            handleGetExamResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'schedule':
        if ($requestMethod === 'GET') {
            handleGetExamSchedule();
        } elseif ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleScheduleExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        if ($examId && $requestMethod === 'GET') {
            handleGetExam($examId);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Exams endpoint not found",
                "available_endpoints" => [
                    "" => "GET - List exams, POST - Create exam (admin/lecturer)",
                    "create" => "POST - Create exam (admin/lecturer)",
                    "update" => "PUT - Update exam (admin/lecturer)",
                    "delete" => "DELETE - Delete exam (admin/lecturer)",
                    "questions" => "GET - Get exam questions, POST - Add question (admin/lecturer)",
                    "take" => "POST - Start taking exam (student only)",
                    "submit" => "POST - Submit exam answers (student only)",
                    "results" => "GET - Get exam results",
                    "schedule" => "GET - Get exam schedule, POST - Schedule exam (admin/lecturer)",
                    "{id}" => "GET - Get specific exam details"
                ]
            ]);
        }
        break;
}

function handleGetExams() {
    $filters = [];
    
    // Get query parameters
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['type'])) $filters['type'] = $_GET['type'];
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $examModel = new ExamModel();
    $exams = $examModel->getExams($filters);
    
    // Apply pagination
    $totalExams = count($exams);
    $exams = array_slice($exams, $offset, $limit);
    
    echo json_encode([
        "success" => true,
        "data" => [
            "exams" => $exams,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $totalExams,
                "pages" => ceil($totalExams / $limit)
            ]
        ]
    ]);
}

function handleGetExam($examId) {
    $examModel = new ExamModel();
    $exam = $examModel->getExam($examId);
    
    if ($exam) {
        echo json_encode([
            "success" => true,
            "data" => $exam
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Exam not found"
        ]);
    }
}

function handleCreateExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Required fields
    $requiredFields = ['title', 'course_id', 'duration', 'total_marks'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Field '$field' is required"
            ]);
            return;
        }
    }
    
    $examData = [
        'title' => $input['title'],
        'course_id' => $input['course_id'],
        'description' => $input['description'] ?? '',
        'duration' => $input['duration'], // in minutes
        'total_marks' => $input['total_marks'],
        'type' => $input['type'] ?? 'multiple_choice',
        'status' => $input['status'] ?? 'draft',
        'created_by_user_id' => Session::get('user_id'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Optional fields
    if (isset($input['instructions'])) $examData['instructions'] = $input['instructions'];
    if (isset($input['passing_marks'])) $examData['passing_marks'] = $input['passing_marks'];
    if (isset($input['scheduled_date'])) $examData['scheduled_date'] = $input['scheduled_date'];
    
    $examModel = new ExamModel();
    if ($examModel->createExam($examData)) {
        echo json_encode([
            "success" => true,
            "message" => "Exam created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create exam"
        ]);
    }
}

function handleUpdateExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID is required"
        ]);
        return;
    }
    
    $examId = $input['id'];
    $examModel = new ExamModel();
    $existingExam = $examModel->getExam($examId);
    
    if (!$existingExam) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Exam not found"
        ]);
        return;
    }
    
    // Allowed fields for update
    $allowedFields = ['title', 'description', 'duration', 'total_marks', 'type', 'status', 'instructions', 'passing_marks', 'scheduled_date'];
    $updateData = [];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateData[$field] = $input[$field];
        }
    }
    
    if (empty($updateData)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "No valid fields to update"
        ]);
        return;
    }
    
    if ($examModel->updateExam($examId, $updateData)) {
        echo json_encode([
            "success" => true,
            "message" => "Exam updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update exam"
        ]);
    }
}

function handleDeleteExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID is required"
        ]);
        return;
    }
    
    $examId = $input['id'];
    $examModel = new ExamModel();
    
    if ($examModel->deleteExam($examId)) {
        echo json_encode([
            "success" => true,
            "message" => "Exam deleted successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to delete exam"
        ]);
    }
}

function handleGetExamQuestions() {
    if (!isset($_GET['exam_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID is required"
        ]);
        return;
    }
    
    $examId = $_GET['exam_id'];
    $examModel = new ExamModel();
    $questions = $examModel->getExamQuestions($examId);
    
    echo json_encode([
        "success" => true,
        "data" => $questions
    ]);
}

function handleAddExamQuestion() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_id']) || !isset($input['question']) || !isset($input['type'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID, question, and type are required"
        ]);
        return;
    }
    
    $questionData = [
        'exam_id' => $input['exam_id'],
        'question' => $input['question'],
        'type' => $input['type'], // multiple_choice, true_false, essay
        'marks' => $input['marks'] ?? 1,
        'options' => isset($input['options']) ? json_encode($input['options']) : null,
        'correct_answer' => $input['correct_answer'] ?? null,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $examModel = new ExamModel();
    if ($examModel->addQuestion($questionData)) {
        echo json_encode([
            "success" => true,
            "message" => "Question added successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to add question"
        ]);
    }
}

function handleTakeExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID is required"
        ]);
        return;
    }
    
    $examId = $input['exam_id'];
    $studentId = Session::get('user_id');
    
    $examModel = new ExamModel();
    
    // Check if student is enrolled in the course
    if (!$examModel->isStudentEnrolled($examId, $studentId)) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "error" => "You are not enrolled in this course"
        ]);
        return;
    }
    
    // Check if exam is available
    if (!$examModel->isExamAvailable($examId)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam is not available at this time"
        ]);
        return;
    }
    
    // Start exam session
    $examSession = $examModel->startExam($examId, $studentId);
    
    if ($examSession) {
        // Get exam questions (without correct answers for students)
        $questions = $examModel->getExamQuestionsForStudent($examId);
        
        echo json_encode([
            "success" => true,
            "message" => "Exam started successfully",
            "data" => [
                "exam_session_id" => $examSession['id'],
                "start_time" => $examSession['start_time'],
                "duration" => $examSession['duration'],
                "questions" => $questions
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to start exam"
        ]);
    }
}

function handleSubmitExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_session_id']) || !isset($input['answers'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam session ID and answers are required"
        ]);
        return;
    }
    
    $examSessionId = $input['exam_session_id'];
    $answers = $input['answers'];
    $studentId = Session::get('user_id');
    
    $examModel = new ExamModel();
    
    // Submit exam and calculate results
    $result = $examModel->submitExam($examSessionId, $studentId, $answers);
    
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Exam submitted successfully",
            "data" => [
                "score" => $result['score'],
                "total_marks" => $result['total_marks'],
                "percentage" => $result['percentage'],
                "passed" => $result['passed']
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to submit exam"
        ]);
    }
}

function handleGetExamResults() {
    $filters = [];
    
    if (isset($_GET['exam_id'])) $filters['exam_id'] = $_GET['exam_id'];
    if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    
    $resultModel = new ResultModel();
    $results = $resultModel->getResults($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $results
    ]);
}

function handleGetExamSchedule() {
    $filters = [];
    
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['date'])) $filters['date'] = $_GET['date'];
    
    $examModel = new ExamModel();
    $schedule = $examModel->getExamSchedule($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $schedule
    ]);
}

function handleScheduleExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_id']) || !isset($input['scheduled_date'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID and scheduled date are required"
        ]);
        return;
    }
    
    $examId = $input['exam_id'];
    $scheduledDate = $input['scheduled_date'];
    
    $examModel = new ExamModel();
    if ($examModel->scheduleExam($examId, $scheduledDate)) {
        echo json_encode([
            "success" => true,
            "message" => "Exam scheduled successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to schedule exam"
        ]);
    }
}
