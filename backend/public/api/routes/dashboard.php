<?php
// Dashboard API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';

// Check authentication for all dashboard operations
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
            handleGetDashboard();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'admin':
        if ($requestMethod === 'GET' && $isAdmin) {
            handleGetAdminDashboard();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'lecturer':
        if ($requestMethod === 'GET' && $isLecturer) {
            handleGetLecturerDashboard();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'student':
        if ($requestMethod === 'GET' && $isStudent) {
            handleGetStudentDashboard();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'stats':
        if ($requestMethod === 'GET') {
            handleGetStats();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'recent-activity':
        if ($requestMethod === 'GET') {
            handleGetRecentActivity();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Dashboard endpoint not found",
            "available_endpoints" => [
                "" => "GET - Get general dashboard data",
                "admin" => "GET - Get admin dashboard (admin only)",
                "lecturer" => "GET - Get lecturer dashboard (lecturer only)",
                "student" => "GET - Get student dashboard (student only)",
                "stats" => "GET - Get general statistics",
                "recent-activity" => "GET - Get recent system activity"
            ]
        ]);
        break;
}

function handleGetDashboard() {
    // Get general dashboard data based on user role
    $userId = Session::get('user_id');
    $userRole = $currentUserData->role_id;
    
    $dashboardData = [
        'user_info' => [
            'id' => $currentUserData->id,
            'name' => $currentUserData->name,
            'role' => $userRole,
            'last_login' => $currentUserData->last_login ?? null
        ],
        'quick_stats' => getQuickStats($userId, $userRole),
        'recent_notifications' => getRecentNotifications($userId)
    ];
    
    echo json_encode([
        "success" => true,
        "data" => $dashboardData
    ]);
}

function handleGetAdminDashboard() {
    $dashboardData = [
        'system_overview' => [
            'total_users' => getTotalUsers(),
            'total_courses' => getTotalCourses(),
            'total_exams' => getTotalExams(),
            'active_enrollments' => getActiveEnrollments()
        ],
        'user_distribution' => [
            'students' => getUsersByRole(2),
            'lecturers' => getUsersByRole(3),
            'admins' => getUsersByRole(1)
        ],
        'course_stats' => [
            'active_courses' => getActiveCourses(),
            'courses_with_exams' => getCoursesWithExams(),
            'popular_courses' => getPopularCourses()
        ],
        'exam_stats' => [
            'scheduled_exams' => getScheduledExams(),
            'completed_exams' => getCompletedExams(),
            'pending_results' => getPendingResults()
        ],
        'recent_activities' => getAdminRecentActivities(),
        'system_health' => getSystemHealth()
    ];
    
    echo json_encode([
        "success" => true,
        "data" => $dashboardData
    ]);
}

function handleGetLecturerDashboard() {
    $userId = Session::get('user_id');
    
    $dashboardData = [
        'my_courses' => [
            'total_courses' => getLecturerCourseCount($userId),
            'active_courses' => getLecturerActiveCourses($userId),
            'courses_with_exams' => getLecturerCoursesWithExams($userId)
        ],
        'student_stats' => [
            'total_students' => getLecturerTotalStudents($userId),
            'enrolled_students' => getLecturerEnrolledStudents($userId)
        ],
        'exam_stats' => [
            'total_exams' => getLecturerExamCount($userId),
            'scheduled_exams' => getLecturerScheduledExams($userId),
            'pending_grading' => getLecturerPendingGrading($userId)
        ],
        'recent_activities' => getLecturerRecentActivities($userId),
        'upcoming_deadlines' => getLecturerUpcomingDeadlines($userId)
    ];
    
    echo json_encode([
        "success" => true,
        "data" => $dashboardData
    ]);
}

function handleGetStudentDashboard() {
    $userId = Session::get('user_id');
    
    $dashboardData = [
        'my_courses' => [
            'enrolled_courses' => getStudentEnrolledCourses($userId),
            'completed_courses' => getStudentCompletedCourses($userId)
        ],
        'exam_info' => [
            'upcoming_exams' => getStudentUpcomingExams($userId),
            'completed_exams' => getStudentCompletedExams($userId),
            'pending_results' => getStudentPendingResults($userId)
        ],
        'academic_progress' => [
            'overall_average' => getStudentOverallAverage($userId),
            'courses_in_progress' => getStudentCoursesInProgress($userId)
        ],
        'recent_activities' => getStudentRecentActivities($userId),
        'notifications' => getStudentNotifications($userId)
    ];
    
    echo json_encode([
        "success" => true,
        "data" => $dashboardData
    ]);
}

function handleGetStats() {
    $filters = [];
    
    if (isset($_GET['period'])) $filters['period'] = $_GET['period'];
    if (isset($_GET['course_id'])) $filters['course_id'] = $_GET['course_id'];
    if (isset($_GET['semester'])) $filters['semester'] = $_GET['semester'];
    
    $stats = [
        'enrollment_trends' => getEnrollmentTrends($filters),
        'exam_performance' => getExamPerformanceStats($filters),
        'course_popularity' => getCoursePopularityStats($filters),
        'user_activity' => getUserActivityStats($filters)
    ];
    
    echo json_encode([
        "success" => true,
        "data" => $stats
    ]);
}

function handleGetRecentActivity() {
    $filters = [];
    
    if (isset($_GET['limit'])) $filters['limit'] = (int)$_GET['limit'];
    if (isset($_GET['type'])) $filters['type'] = $_GET['type'];
    
    $activities = getRecentActivities($filters);
    
    echo json_encode([
        "success" => true,
        "data" => $activities
    ]);
}

// Helper functions for dashboard data
function getQuickStats($userId, $userRole) {
    switch ($userRole) {
        case 1: // Admin
            return [
                'total_users' => getTotalUsers(),
                'total_courses' => getTotalCourses(),
                'total_exams' => getTotalExams()
            ];
        case 2: // Student
            return [
                'enrolled_courses' => getStudentEnrolledCourseCount($userId),
                'upcoming_exams' => getStudentUpcomingExamCount($userId),
                'completed_exams' => getStudentCompletedExamCount($userId)
            ];
        case 3: // Lecturer
            return [
                'my_courses' => getLecturerCourseCount($userId),
                'total_students' => getLecturerTotalStudents($userId),
                'pending_grading' => getLecturerPendingGradingCount($userId)
            ];
        default:
            return [];
    }
}

function getTotalUsers() {
    $userModel = new UserModel(DB::getInstance());
    return $userModel->getTotalUsers();
}

function getTotalCourses() {
    $courseModel = new CourseMode();
    return $courseModel->getTotalCourses();
}

function getTotalExams() {
    $examModel = new ExamModel();
    return $examModel->getTotalExams();
}

function getActiveEnrollments() {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getActiveEnrollmentCount();
}

function getUsersByRole($roleId) {
    $userModel = new UserModel(DB::getInstance());
    return $userModel->getUsersByRole($roleId);
}

function getActiveCourses() {
    $courseModel = new CourseMode();
    return $courseModel->getActiveCourseCount();
}

function getCoursesWithExams() {
    $courseModel = new CourseMode();
    return $courseModel->getCoursesWithExamsCount();
}

function getPopularCourses() {
    $courseModel = new CourseMode();
    return $courseModel->getPopularCourses(5); // Top 5
}

function getScheduledExams() {
    $examModel = new ExamModel();
    return $examModel->getScheduledExamCount();
}

function getCompletedExams() {
    $examModel = new ExamModel();
    return $examModel->getCompletedExamCount();
}

function getPendingResults() {
    $resultModel = new ResultModel();
    return $resultModel->getPendingResultCount();
}

function getLecturerCourseCount($lecturerId) {
    $courseModel = new CourseMode();
    return $courseModel->getLecturerCourseCount($lecturerId);
}

function getLecturerActiveCourses($lecturerId) {
    $courseModel = new CourseMode();
    return $courseModel->getLecturerActiveCourses($lecturerId);
}

function getLecturerCoursesWithExams($lecturerId) {
    $courseModel = new CourseMode();
    return $courseModel->getLecturerCoursesWithExams($lecturerId);
}

function getLecturerTotalStudents($lecturerId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getLecturerTotalStudents($lecturerId);
}

function getLecturerEnrolledStudents($lecturerId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getLecturerEnrolledStudents($lecturerId);
}

function getLecturerExamCount($lecturerId) {
    $examModel = new ExamModel();
    return $examModel->getLecturerExamCount($lecturerId);
}

function getLecturerScheduledExams($lecturerId) {
    $examModel = new ExamModel();
    return $examModel->getLecturerScheduledExams($lecturerId);
}

function getLecturerPendingGrading($lecturerId) {
    $resultModel = new ResultModel();
    return $resultModel->getLecturerPendingGrading($lecturerId);
}

function getStudentEnrolledCourses($studentId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getStudentCourses($studentId);
}

function getStudentCompletedCourses($studentId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getStudentCompletedCourses($studentId);
}

function getStudentUpcomingExams($studentId) {
    $examModel = new ExamModel();
    return $examModel->getStudentUpcomingExams($studentId);
}

function getStudentCompletedExams($studentId) {
    $examModel = new ExamModel();
    return $examModel->getStudentCompletedExams($studentId);
}

function getStudentPendingResults($studentId) {
    $resultModel = new ResultModel();
    return $resultModel->getStudentPendingResults($studentId);
}

function getStudentOverallAverage($studentId) {
    $resultModel = new ResultModel();
    return $resultModel->getStudentOverallAverage($studentId);
}

function getStudentCoursesInProgress($studentId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getStudentCoursesInProgress($studentId);
}

// Placeholder functions for notifications and activities
function getRecentNotifications($userId) {
    // This would typically come from a notifications table
    return [];
}

function getAdminRecentActivities() {
    // This would typically come from an audit log table
    return [];
}

function getLecturerRecentActivities($lecturerId) {
    // This would typically come from an audit log table
    return [];
}

function getStudentRecentActivities($studentId) {
    // This would typically come from an audit log table
    return [];
}

function getLecturerUpcomingDeadlines($lecturerId) {
    // This would typically come from exams and assignments
    return [];
}

function getStudentNotifications($studentId) {
    // This would typically come from a notifications table
    return [];
}

function getEnrollmentTrends($filters) {
    // This would typically calculate enrollment trends over time
    return [];
}

function getExamPerformanceStats($filters) {
    // This would typically calculate exam performance statistics
    return [];
}

function getCoursePopularityStats($filters) {
    // This would typically calculate course popularity metrics
    return [];
}

function getUserActivityStats($filters) {
    // This would typically calculate user activity metrics
    return [];
}

function getRecentActivities($filters) {
    // This would typically come from an audit log table
    return [];
}

function getSystemHealth() {
    // This would typically check system status, database connections, etc.
    return [
        'database' => 'healthy',
        'file_system' => 'healthy',
        'memory_usage' => 'normal',
        'last_backup' => date('Y-m-d H:i:s')
    ];
}

// Additional helper functions for counts
function getStudentEnrolledCourseCount($studentId) {
    $enrollmentModel = new EnrollmentModel();
    return $enrollmentModel->getStudentEnrolledCourseCount($studentId);
}

function getStudentUpcomingExamCount($studentId) {
    $examModel = new ExamModel();
    return $examModel->getStudentUpcomingExamCount($studentId);
}

function getStudentCompletedExamCount($studentId) {
    $examModel = new ExamModel();
    return $examModel->getStudentCompletedExamCount($studentId);
}

function getLecturerPendingGradingCount($lecturerId) {
    $resultModel = new ResultModel();
    return $resultModel->getLecturerPendingGradingCount($lecturerId);
}
