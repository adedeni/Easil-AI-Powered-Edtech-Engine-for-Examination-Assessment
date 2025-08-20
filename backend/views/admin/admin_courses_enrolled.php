<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) { echo '<p>No course selected.</p>'; exit; }
$students = $courseModel->getEnrolledStudents($id);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Enrolled Students</title></head>
<body>
<h1>Enrolled Students</h1>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Username</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student->name); ?></td>
            <td><?php echo htmlspecialchars($student->username); ?></td>
            <td><?php echo htmlspecialchars($student->email); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
