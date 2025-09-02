<?php
// Results API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';
$resultId = isset($requestUri[3]) ? $requestUri[3] : null;

// Check authentication for all result operations
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
            handleGetResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'my-results':
        if ($requestMethod === 'GET' && $isStudent) {
            handleGetMyResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'course-results':
        if ($requestMethod === 'GET' && ($isAdmin || $isLecturer)) {
            handleGetCourseResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'exam-results':
        if ($requestMethod === 'GET') {
            handleGetExamResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'grade':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleGradeExam();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'bulk-grade':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleBulkGrade();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'analytics':
        if ($requestMethod === 'GET') {
            handleGetAnalytics();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'export':
        if ($requestMethod === 'GET' && ($isAdmin || $isLecturer)) {
            handleExportResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'publish':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handlePublishResults();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        if ($resultId && $requestMethod === 'GET') {
            handleGetResult($resultId);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Results endpoint not found",
                "available_endpoints" => [
                    "" => "GET - List all results",
                    "my-results" => "GET - Get my results (student only)",
                    "course-results" => "GET - Get results for a course (admin/lecturer)",
                    "exam-results" => "GET - Get results for an exam",
                    "grade" => "POST - Grade an exam (admin/lecturer)",
                    "bulk-grade" => "POST - Bulk grade exams (admin/lecturer)",
                    "analytics" => "GET - Get result analytics",
                    "export" => "GET - Export results (admin/lecturer)",
                    "publish" => "POST - Publish results (admin/lecturer)",
                    "{id}" => "GET - Get specific result details"
                ]
            ]);
        }
        break;
}

function handleGetResults() {
    $filters = [];
    
    // Get query parameters
    if (isset($_GET['exam_id'])) $filters['exam_id'] = $_GET['exam_id'];
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['exam_id'];
    if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $resultModel = new ResultModel();
    $results = $resultModel->getResults($filters);
    
    // Apply pagination
    $totalResults = count($results);
    $results = array_slice($results, $offset, $limit);
    
    echo json_encode([
        "success" => true,
        "data" => [
            "results" => $results,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $totalResults,
                "pages" => ceil($totalResults / $limit)
            ]
        ]
    ]);
}

function handleGetResult($resultId) {
    $resultModel = new ResultModel();
    $result = $resultModel->getResult($resultId);
    
    if ($result) {
        echo json_encode([
            "success" => true,
            "data" => $result
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Result not found"
        ]);
    }
}

function handleGetMyResults() {
    $studentId = Session::get('user_id');
    
    $resultModel = new ResultModel();
    $results = $resultModel->getStudentResults($studentId);
    
    echo json_encode([
        "success" => true,
        "data" => $results
    ]);
}

function handleGetCourseResults() {
    if (!isset($_GET['course_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID is required"
        ]);
        return;
    }
    
    $courseId = $_GET['course_id'];
    $resultModel = new ResultModel();
    $results = $resultModel->getCourseResults($courseId);
    
    echo json_encode([
        "success" => true,
        "data" => $results
    ]);
}

function handleGetExamResults() {
    if (!isset($_GET['exam_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID is required"
        ]);
        return;
    }
    
    $examId = $_GET['exam_id'];
    $resultModel = new ResultModel();
    $results = $resultModel->getExamResults($examId);
    
    echo json_encode([
        "success" => true,
        "data" => $results
    ]);
}

function handleGradeExam() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_id']) || !isset($input['student_id']) || !isset($input['score'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID, student ID, and score are required"
        ]);
        return;
    }
    
    $examId = $input['exam_id'];
    $studentId = $input['student_id'];
    $score = $input['score'];
    
    $resultModel = new ResultModel();
    
    // Calculate percentage and determine if passed
    $examModel = new ExamModel();
    $exam = $examModel->getExam($examId);
    
    if (!$exam) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Exam not found"
        ]);
        return;
    }
    
    $totalMarks = $exam->total_marks;
    $percentage = ($score / $totalMarks) * 100;
    $passingMarks = $exam->passing_marks ?? ($totalMarks * 0.4); // Default 40%
    $passed = $score >= $passingMarks;
    
    $resultData = [
        'exam_id' => $examId,
        'student_user_id' => $studentId,
        'score' => $score,
        'total_marks' => $totalMarks,
        'percentage' => round($percentage, 2),
        'passed' => $passed,
        'graded_by_user_id' => Session::get('user_id'),
        'graded_at' => date('Y-m-d H:i:s'),
        'status' => 'graded'
    ];
    
    // Optional fields
    if (isset($input['comments'])) $resultData['comments'] = $input['comments'];
    if (isset($input['grade'])) $resultData['grade'] = $input['grade'];
    
    if ($resultModel->createOrUpdateResult($resultData)) {
        echo json_encode([
            "success" => true,
            "message" => "Exam graded successfully",
            "data" => [
                "score" => $score,
                "total_marks" => $totalMarks,
                "percentage" => round($percentage, 2),
                "passed" => $passed
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to grade exam"
        ]);
    }
}

function handleBulkGrade() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['exam_id']) || !isset($input['grades']) || !is_array($input['grades'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Exam ID and grades array are required"
        ]);
        return;
    }
    
    $examId = $input['exam_id'];
    $grades = $input['grades'];
    
    $resultModel = new ResultModel();
    $examModel = new ExamModel();
    $exam = $examModel->getExam($examId);
    
    if (!$exam) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Exam not found"
        ]);
        return;
    }
    
    $totalMarks = $exam->total_marks;
    $passingMarks = $exam->passing_marks ?? ($totalMarks * 0.4);
    $results = [];
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($grades as $grade) {
        try {
            if (!isset($grade['student_id']) || !isset($grade['score'])) {
                $results[] = [
                    "student_id" => $grade['student_id'] ?? 'unknown',
                    "success" => false,
                    "error" => "Missing student_id or score"
                ];
                $errorCount++;
                continue;
            }
            
            $score = $grade['score'];
            $percentage = ($score / $totalMarks) * 100;
            $passed = $score >= $passingMarks;
            
            $resultData = [
                'exam_id' => $examId,
                'student_user_id' => $grade['student_id'],
                'score' => $score,
                'total_marks' => $totalMarks,
                'percentage' => round($percentage, 2),
                'passed' => $passed,
                'graded_by_user_id' => Session::get('user_id'),
                'graded_at' => date('Y-m-d H:i:s'),
                'status' => 'graded'
            ];
            
            if (isset($grade['comments'])) $resultData['comments'] = $grade['comments'];
            if (isset($grade['grade'])) $resultData['grade'] = $grade['grade'];
            
            if ($resultModel->createOrUpdateResult($resultData)) {
                $results[] = [
                    "student_id" => $grade['student_id'],
                    "success" => true,
                    "message" => "Graded successfully"
                ];
                $successCount++;
            } else {
                $results[] = [
                    "student_id" => $grade['student_id'],
                    "success" => false,
                    "error" => "Failed to grade"
                ];
                $errorCount++;
            }
            
        } catch (Exception $e) {
            $results[] = [
                "student_id" => $grade['student_id'] ?? 'unknown',
                "success" => false,
                "error" => "Exception: " . $e->getMessage()
            ];
            $errorCount++;
        }
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Bulk grading completed",
        "data" => [
            "total" => count($grades),
            "successful" => $successCount,
            "failed" => $errorCount,
            "results" => $results
        ]
    ]);
}

function handleGetAnalytics() {
    $filters = [];
    
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['exam_id'])) $filters['exam_id'] = $_GET['exam_id'];
    if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
    if (isset($_GET['academic_year'])) $filters['academic_year'] = $_GET['academic_year'];
    
    $resultModel = new ResultModel();
    $analytics = $resultModel->getAnalytics($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $analytics
    ]);
}

function handleExportResults() {
    $filters = [];
    
    if (isset($_GET['exam_id'])) $filters['exam_id'] = $_GET['exam_id'];
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
    
    $resultModel = new ResultModel();
    $results = $resultModel->getResults($filters);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="results_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, ['Student ID', 'Student Name', 'Exam Title', 'Course', 'Score', 'Total Marks', 'Percentage', 'Grade', 'Passed', 'Graded Date']);
    
    // CSV data
    foreach ($results as $result) {
        fputcsv($output, [
            $result->student_user_id,
            $result->student_name ?? '',
            $result->exam_title ?? '',
            $result->course_title ?? '',
            $result->score,
            $result->total_marks,
            $result->percentage,
            $result->grade ?? '',
            $result->passed ? 'Yes' : 'No',
            $result->graded_at
        ]);
    }
    
    fclose($output);
    exit();
}

function handlePublishResults() {
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
    $resultModel = new ResultModel();
    
    if ($resultModel->publishResults($examId)) {
        echo json_encode([
            "success" => true,
            "message" => "Results published successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to publish results"
        ]);
    }
}
