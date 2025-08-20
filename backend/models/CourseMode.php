<?php
class CourseMode {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

	// Create a new course
	public function createCourse($data) {
    $fields = [
        'title' => $data['title'],
        'code' => $data['code'],
        'description' => $data['description'] ?? '',
        'status' => $data['status'] ?? 'active',
        'coordinator_user_id' => !empty($data['coordinator_user_id']) ? $data['coordinator_user_id'] : null, 
        'department' => $data['department'] ?? null,
        'materials' => $data['materials'] ?? null,
        'created_by_user_id' => $data['created_by_user_id'] ?? null
    ];

    
    return $this->db->insert('courses', $fields);
}

	// Bulk import courses
	public function bulkImportCourses($courses) {
		$results = [];
		foreach ($courses as $course) {
			$results[] = $this->createCourse($course);
		}
		return $results;
	}

	// Get courses with filters
	public function getCourses($filters = []) {
		$sql = "SELECT c.*, u.name as coordinator FROM courses c LEFT JOIN users u ON c.coordinator_user_id = u.id WHERE 1=1";
		$params = [];
		if (!empty($filters['keywords'])) {
			$sql .= " AND (c.title LIKE ? OR c.code LIKE ? OR c.description LIKE ?)";
			$kw = "%" . $filters['keywords'] . "%";
			$params[] = $kw; $params[] = $kw; $params[] = $kw;
		}
		if (!empty($filters['department'])) {
			$sql .= " AND c.department = ?";
			$params[] = $filters['department'];
		}
		if (!empty($filters['title'])) {
			$sql .= " AND c.title LIKE ?";
			$params[] = "%" . $filters['title'] . "%";
		}
		if (!empty($filters['code'])) {
			$sql .= " AND c.code LIKE ?";
			$params[] = "%" . $filters['code'] . "%";
		}
		if (!empty($filters['status'])) {
			$sql .= " AND c.status = ?";
			$params[] = $filters['status'];
		}
		if (!empty($filters['date'])) {
			$sql .= " AND DATE(c.created_at) = ?";
			$params[] = $filters['date'];
		}
		$sql .= " ORDER BY c.created_at DESC";
		$this->db->query($sql, $params);
		return $this->db->results();
	}

	// Update course
	public function updateCourse($id, $data) {
		return $this->db->update('courses', $id, $data);
	}

	// Delete course
	public function deleteCourse($id) {
		return $this->db->delete('courses', ['id', '=', $id]);
	}

	// Enroll students (single or bulk)
	public function enrollStudents($courseId, $studentIds) {
		$results = [];
		foreach ($studentIds as $studentId) {
			$fields = [
				'student_user_id' => $studentId,
				'course_id' => $courseId
			];
			$results[] = $this->db->insert('enrollments', $fields);
		}
		return $results;
	}

	// Get enrolled students for a course
	public function getEnrolledStudents($courseId) {
		$sql = "SELECT u.id, u.name, u.username, u.email FROM enrollments e JOIN users u ON e.student_user_id = u.id WHERE e.course_id = ?";
		$this->db->query($sql, [$courseId]);
		return $this->db->results();
	}

	// Get course details
	public function getCourseDetails($id) {
    // Select the user's name and username, aliasing them as 'coordinator' and 'coordinator_username'
    $sql = "SELECT c.*, u.name as coordinator, u.username as coordinator_username FROM courses c LEFT JOIN users u ON c.coordinator_user_id = u.id WHERE c.id = ?";
    $this->db->query($sql, [$id]);
    return $this->db->first();
	    
	}
	
	public function getCoordinators() {
 
    $sql = "SELECT id, username, name FROM users WHERE user_group_id = 2 ORDER BY username"; 
    return $this->db->query($sql)->results();
	    
	}


	 public function getDatabaseError() {
        return $this->db->getError();
    }
}
