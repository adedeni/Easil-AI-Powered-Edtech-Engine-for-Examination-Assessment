<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) { echo '<p>No course selected.</p>'; exit; }
$course = $courseModel->getCourseDetails($id);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Course Details</title></head>
<body>
<h1>Course Details</h1>
<ul>
    <li>Title: <?php echo htmlspecialchars($course->title); ?></li>
    <li>Code: <?php echo htmlspecialchars($course->code); ?></li>
    <li>Status: <?php echo htmlspecialchars($course->status); ?></li>
    <li>Department: <?php echo htmlspecialchars($course->department); ?></li>
    <li>Coordinator: <?php echo htmlspecialchars($course->coordinator); ?></li>
    <li>Materials: <?php echo htmlspecialchars($course->materials); ?></li>
    <li>Description: <?php echo htmlspecialchars($course->description); ?></li>
    <li>Created At: <?php echo htmlspecialchars($course->created_at); ?></li>
</ul>
</body>
</html>
