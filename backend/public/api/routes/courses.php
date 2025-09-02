<?php
// Courses API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';
$courseId = isset($requestUri[3]) ? $requestUri[3] : null;

// Check authentication for all course operations
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

switch ($action) {
    case '':
        if ($requestMethod === 'GET') {
            handleGetCourses();
        } elseif ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateCourse();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'create':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleCreateCourse();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'update':
        if ($requestMethod === 'PUT' && ($isAdmin || $isLecturer)) {
            handleUpdateCourse();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'delete':
        if ($requestMethod === 'DELETE' && $isAdmin) {
            handleDeleteCourse();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'bulk-import':
        if ($requestMethod === 'POST' && $isAdmin) {
            handleBulkImportCourses();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'assign-lecturer':
        if ($requestMethod === 'POST' && $isAdmin) {
            handleAssignLecturer();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'enroll-students':
        if ($requestMethod === 'POST' && ($isAdmin || $isLecturer)) {
            handleEnrollStudents();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'enrolled-students':
        if ($requestMethod === 'GET') {
            handleGetEnrolledStudents();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'available':
        if ($requestMethod === 'GET') {
            handleGetAvailableCourses();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        if ($courseId && $requestMethod === 'GET') {
            handleGetCourse($courseId);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Courses endpoint not found",
                "available_endpoints" => [
                    "" => "GET - List courses, POST - Create course (admin/lecturer)",
                    "create" => "POST - Create course (admin/lecturer)",
                    "update" => "PUT - Update course (admin/lecturer)",
                    "delete" => "DELETE - Delete course (admin only)",
                    "bulk-import" => "POST - Bulk import courses (admin only)",
                    "assign-lecturer" => "POST - Assign lecturer to course (admin only)",
                    "enroll-students" => "POST - Enroll students in course (admin/lecturer)",
                    "enrolled-students" => "GET - Get enrolled students for course",
                    "available" => "GET - Get available courses for enrollment",
                    "{id}" => "GET - Get specific course details"
                ]
            ]);
        }
        break;
}

function handleGetCourses() {
    $filters = [];
    
    // Get query parameters
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['coordinator_id'])) $filters['coordinator_user_id'] = $_GET['coordinator_id'];
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $courseModel = new CourseMode();
    $courses = $courseModel->getCourses($filters);
    
    // Apply pagination
    $totalCourses = count($courses);
    $courses = array_slice($courses, $offset, $limit);
    
    echo json_encode([
        "success" => true,
        "data" => [
            "courses" => $courses,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $totalCourses,
                "pages" => ceil($totalCourses / $limit)
            ]
        ]
    ]);
}

function handleGetCourse($courseId) {
    $courseModel = new CourseMode();
    $course = $courseModel->getCourseDetails($courseId);
    
    if ($course) {
        echo json_encode([
            "success" => true,
            "data" => $course
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Course not found"
        ]);
    }
}

function handleCreateCourse() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Required fields
    $requiredFields = ['title', 'code', 'description'];
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
    
    // Check if course code already exists
    $courseModel = new CourseMode();
    $existingCourse = $courseModel->getCourseByCode($input['code']);
    if ($existingCourse) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course code already exists"
        ]);
        return;
    }
    
    $courseData = [
        'title' => $input['title'],
        'code' => $input['code'],
        'description' => $input['description'],
        'status' => $input['status'] ?? 'active',
        'department' => $input['department'] ?? null,
        'materials' => $input['materials'] ?? null,
        'created_by_user_id' => Session::get('user_id'),
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Optional fields
    if (isset($input['coordinator_user_id'])) $courseData['coordinator_user_id'] = $input['coordinator_user_id'];
    
    if ($courseModel->createCourse($courseData)) {
        echo json_encode([
            "success" => true,
            "message" => "Course created successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create course"
        ]);
    }
}

function handleUpdateCourse() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID is required"
        ]);
        return;
    }
    
    $courseId = $input['id'];
    $courseModel = new CourseMode();
    $existingCourse = $courseModel->getCourseDetails($courseId);
    
    if (!$existingCourse) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Course not found"
        ]);
        return;
    }
    
    // Allowed fields for update
    $allowedFields = ['title', 'description', 'status', 'department', 'materials', 'coordinator_user_id'];
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
    
    if ($courseModel->updateCourse($courseId, $updateData)) {
        echo json_encode([
            "success" => true,
            "message" => "Course updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update course"
        ]);
    }
}

function handleDeleteCourse() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID is required"
        ]);
        return;
    }
    
    $courseId = $input['id'];
    $courseModel = new CourseMode();
    
    if ($courseModel->deleteCourse($courseId)) {
        echo json_encode([
            "success" => true,
            "message" => "Course deleted successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to delete course"
        ]);
    }
}

function handleBulkImportCourses() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['courses']) || !is_array($input['courses'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Courses array is required"
        ]);
        return;
    }
    
    $courseModel = new CourseMode();
    $results = $courseModel->bulkImportCourses($input['courses']);
    
    $successCount = count(array_filter($results, function($result) { return $result; }));
    $errorCount = count($results) - $successCount;
    
    echo json_encode([
        "success" => true,
        "message" => "Bulk import completed",
        "data" => [
            "total" => count($input['courses']),
            "successful" => $successCount,
            "failed" => $errorCount
        ]
    ]);
}

function handleAssignLecturer() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['course_id']) || !isset($input['lecturer_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID and lecturer ID are required"
        ]);
        return;
    }
    
    $courseId = $input['course_id'];
    $lecturerId = $input['lecturer_id'];
    
    // Verify lecturer exists and has lecturer role
    $userModel = new UserModel(DB::getInstance());
    $lecturer = $userModel->getUser($lecturerId);
    
    if (!$lecturer || $lecturer->role_id != 3) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid lecturer ID or user is not a lecturer"
        ]);
        return;
    }
    
    $courseModel = new CourseMode();
    if ($courseModel->updateCourse($courseId, ['coordinator_user_id' => $lecturerId])) {
        echo json_encode([
            "success" => true,
            "message" => "Lecturer assigned to course successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to assign lecturer to course"
        ]);
    }
}

function handleEnrollStudents() {
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
    
    // Verify all students exist and have student role
    $userModel = new UserModel(DB::getInstance());
    foreach ($studentIds as $studentId) {
        $student = $userModel->getUser($studentId);
        if (!$student || $student->role_id != 2) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Invalid student ID $studentId or user is not a student"
            ]);
            return;
        }
    }
    
    $courseModel = new CourseMode();
    $results = $courseModel->enrollStudents($courseId, $studentIds);
    
    $successCount = count(array_filter($results, function($result) { return $result; }));
    $errorCount = count($results) - $successCount;
    
    echo json_encode([
        "success" => true,
        "message" => "Student enrollment completed",
        "data" => [
            "total" => count($studentIds),
            "successful" => $successCount,
            "failed" => $errorCount
        ]
    ]);
}

function handleGetEnrolledStudents() {
    if (!isset($_GET['course_id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Course ID is required"
        ]);
        return;
    }
    
    $courseId = $_GET['course_id'];
    $courseModel = new CourseMode();
    $students = $courseModel->getEnrolledStudents($courseId);
    
    echo json_encode([
        "success" => true,
        "data" => $students
    ]);
}

function handleGetAvailableCourses() {
    // Get courses that are available for enrollment (active status)
    $filters = ['status' => 'active'];
    
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    
    $courseModel = new CourseMode();
    $courses = $courseModel->getCourses($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $courses
    ]);
}
