<?php
require_once __DIR__ . '/../models/CourseMode.php';
require_once __DIR__ . '/../app/Classes/DB.php';

class CourseController {
	private $courseModel;

	public function __construct() {
		$db = new DB();
		$this->courseModel = new CourseMode($db);
	}

	public function create($data) {
		return $this->courseModel->createCourse($data);
	}

	public function bulkImport($courses) {
		return $this->courseModel->bulkImportCourses($courses);
	}

	public function list($filters = []) {
		return $this->courseModel->getCourses($filters);
	}

	public function update($id, $data) {
		return $this->courseModel->updateCourse($id, $data);
	}

	public function delete($id) {
		return $this->courseModel->deleteCourse($id);
	}

	public function enroll($courseId, $studentIds) {
		return $this->courseModel->enrollStudents($courseId, $studentIds);
	}

	public function enrolledStudents($courseId) {
		return $this->courseModel->getEnrolledStudents($courseId);
	}

	public function details($id) {
		return $this->courseModel->getCourseDetails($id);
	}
}
