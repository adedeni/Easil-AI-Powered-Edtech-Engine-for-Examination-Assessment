<?php
// Enrollments API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';
$enrollmentId = isset($requestUri[3]) ? $requestUri[3] : null;

// Check authentication for all enrollment operations
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
            handleGetEnrollments();
        } elseif ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateEnrollment();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'create':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateEnrollment();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'bulk-enroll':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleBulkEnroll();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'remove':
        if ($requestMethod === 'DELETE' && ($isAdmin || $isLecturer)) {
            handleRemoveEnrollment();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'my-courses':
        if ($requestMethod === 'GET' && $isStudent) {
            handleGetMyCourses();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'course-students':
        if ($requestMethod === 'GET' && ($isAdmin || $isLecturer)) {
            handleGetCourseStudents();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'stats':
        if ($requestMethod === 'GET') {
            handleGetEnrollmentStats();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        if ($enrollmentId && $requestMethod === 'GET') {
            handleGetEnrollment($enrollmentId);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Enrollments endpoint not found",
                "available_endpoints" => [
                    "" => "GET - List enrollments, POST - Create enrollment (admin/lecturer)",
                    "create" => "POST - Create enrollment (admin/lecturer)",
                    "bulk-enroll" => "POST - Bulk enroll students (admin/lecturer)",
                    "remove" => "DELETE - Remove enrollment (admin/lecturer)",
                    "my-courses" => "GET - Get my enrolled courses (student only)",
                    "course-students" => "GET - Get students in a course (admin/lecturer)",
                    "stats" => "GET - Get enrollment statistics",
                    "{id}" => "GET - Get specific enrollment details"
                ]
            ]);
        }
        break;
}

function handleGetEnrollments() {
    $filters = [];
    
    // Get query parameters
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['student_id'])) $filters['student_id'] = $_GET['student_id'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $enrollmentModel = new EnrollmentModel();
    $enrollments = $enrollmentModel->getEnrollments($filters);
    
    // Apply pagination
    $totalEnrollments = count($enrollments);
    $enrollments = array_slice($enrollments, $offset, $limit);
    
    echo json_encode([
        "success" => true,
        "data" => [
            "enrollments" => $enrollments,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $totalEnrollments,
                "pages" => ceil($totalEnrollments / $limit)
            ]
        ]
    ]);
}

function handleGetEnrollment($enrollmentId) {
    $enrollmentModel = new EnrollmentModel();
    $enrollment = $enrollmentModel->getEnrollment($enrollmentId);
    
    if ($enrollment) {
        echo json_encode([
            "success" => true,
            "data" => $enrollment
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Enrollment not found"
        ]);
    }
}

function handleCreateEnrollment() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['course_id']) || !isset($input['student_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID and student ID are required"
        ]);
        return;
    }
    
    $courseId = $input['course_id'];
    $studentId = $input['student_id'];
    
    // Verify student exists and has student role
    $userModel = new UserModel(DB::getInstance());
    $student = $userModel->getUser($studentId);
    
    if (!$student || $student->role_id != 2) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid student ID or user is not a student"
        ]);
        return;
    }
    
    // Check if already enrolled
    $enrollmentModel = new EnrollmentModel();
    if ($enrollmentModel->isEnrolled($courseId, $studentId)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Student is already enrolled in this course"
        ]);
        return;
    }
    
    $enrollmentData = [
        'course_id' => $courseId,
        'student_user_id' => $studentId,
        'enrolled_by_user_id' => Session::get('user_id'),
        'enrollment_date' => date('Y-m-d H:i:s'),
        'status' => 'active'
    ];
    
    // Optional fields
    if (isset($input['semester'])) $enrollmentData['semester'] = $input['semester'];
    if (isset($input['academic_year'])) $enrollmentData['academic_year'] = $input['academic_year'];
    
    if ($enrollmentModel->createEnrollment($enrollmentData)) {
        echo json_encode([
            "success" => true,
            "message" => "Student enrolled successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to enroll student"
        ]);
    }
}

function handleBulkEnroll() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['course_id']) || !isset($input['student_ids']) || !is_array($input['student_ids'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID and student IDs array are required"
        ]);
        return;
    }
    
    $courseId = $input['course_id'];
    $studentIds = $input['student_ids'];
    
    $enrollmentModel = new EnrollmentModel();
    $results = [];
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($studentIds as $studentId) {
        try {
            // Check if already enrolled
            if ($enrollmentModel->isEnrolled($courseId, $studentId)) {
                $results[] = [
                    "student_id" => $studentId,
                    "success" => false,
                    "error" => "Already enrolled"
                ];
                $errorCount++;
                continue;
            }
            
            $enrollmentData = [
                'course_id' => $courseId,
                'student_user_id' => $studentId,
                'enrolled_by_user_id' => Session::get('user_id'),
                'enrollment_date' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ];
            
            if (isset($input['semester'])) $enrollmentData['semester'] = $input['semester'];
            if (isset($input['academic_year'])) $enrollmentData['academic_year'] = $input['academic_year'];
            
            if ($enrollmentModel->createEnrollment($enrollmentData)) {
                $results[] = [
                    "student_id" => $studentId,
                    "success" => true,
                    "message" => "Enrolled successfully"
                ];
                $successCount++;
            } else {
                $results[] = [
                    "student_id" => $studentId,
                    "success" => false,
                    "error" => "Failed to enroll"
                ];
                $errorCount++;
            }
            
        } catch (Exception $e) {
            $results[] = [
                "student_id" => $studentId,
                "success" => false,
                "error" => "Exception: " . $e->getMessage()
            ];
            $errorCount++;
        }
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Bulk enrollment completed",
        "data" => [
            "total" => count($studentIds),
            "successful" => $successCount,
            "failed" => $errorCount,
            "results" => $results
        ]
    ]);
}

function handleRemoveEnrollment() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['course_id']) || !isset($input['student_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID and student ID are required"
        ]);
        return;
    }
    
    $courseId = $input['course_id'];
    $studentId = $input['student_id'];
    
    $enrollmentModel = new EnrollmentModel();
    if ($enrollmentModel->removeEnrollment($courseId, $studentId)) {
        echo json_encode([
            "success" => true,
            "message" => "Enrollment removed successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to remove enrollment"
        ]);
    }
}

function handleGetMyCourses() {
    $studentId = Session::get('user_id');
    
    $enrollmentModel = new EnrollmentModel();
    $courses = $enrollmentModel->getStudentCourses($studentId);
    
    echo json_encode([
        "success" => true,
        "data" => $courses
    ]);
}

function handleGetCourseStudents() {
    if (!isset($_GET['course_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID is required"
        ]);
        return;
    }
    
    $courseId = $_GET['course_id'];
    $enrollmentModel = new EnrollmentModel();
    $students = $enrollmentModel->getCourseStudents($courseId);
    
    echo json_encode([
        "success" => true,
        "data" => $students
    ]);
}

function handleGetEnrollmentStats() {
    $filters = [];
    
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
    if (isset($_GET['academic_year'])) $filters['academic_year'] = $_GET['academic_year'];
    
    $enrollmentModel = new EnrollmentModel();
    $stats = $enrollmentModel->getEnrollmentStats($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $stats
    ]);
}
