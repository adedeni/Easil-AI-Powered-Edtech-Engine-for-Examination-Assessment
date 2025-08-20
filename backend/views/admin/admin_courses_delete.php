<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) { echo '<p>No course selected.</p>'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    $courseModel->deleteCourse($id);
    echo '<p>Course deleted successfully!</p>';
    echo '<meta http-equiv="refresh" content="1;url=admin_courses.php">';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Delete Course</title></head>
<body>
<h1>Delete Course</h1>
<form method="post" action="">
    <p>Are you sure you want to delete this course?</p>
    <button type="submit" name="delete_course">Delete</button>
</form>
</body>
</html>
