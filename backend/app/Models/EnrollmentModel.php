<?php
define('STUDENT_ROLE_ID', 3); // Change 3 to your actual student role ID

class EnrollmentModel {
    public static function findStudent($identifier) {
        $db = DB::getInstance();
        // Try by ID
        $student = $db->get('users', ['id', '=', $identifier])->first();
        if ($student && $student->role_id == STUDENT_ROLE_ID) return (array)$student;
        // Try by email
        $student = $db->get('users', ['email', '=', $identifier])->first();
        if ($student && $student->role_id == STUDENT_ROLE_ID) return (array)$student;
        return false;
    }

    public static function enroll($student_id, $course_id) {
        $db = DB::getInstance();
        // Prevent duplicate enrollment
        $exists = $db->get('enrollments', [
            ['student_user_id', '=', $student_id],
            ['course_id', '=', $course_id]
        ])->count();
        if ($exists) return false;
        return $db->insert('enrollments', [
            'student_user_id' => $student_id,
            'course_id' => $course_id,
            'enrolled_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function getEnrolledStudents($course_id, $keyword = '') {
        $db = DB::getInstance();
        $sql = "SELECT u.id as student_id, u.name, u.email, e.enrolled_at
                FROM enrollments e
                JOIN users u ON e.student_user_id = u.id
                WHERE e.course_id = ?";
        $params = [$course_id];
        if ($keyword) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }
        return $db->query($sql, $params)->results();
    }
}