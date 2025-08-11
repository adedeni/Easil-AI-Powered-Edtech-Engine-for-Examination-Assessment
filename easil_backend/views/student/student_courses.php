<?php
require_once '../../app/Core/init.php';
$user = new User();
if(!$user->isLoggedIn()){
    Redirect::to('../auth/login.php');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
</head>
<body>
    <h1>My Courses</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <?php
    require_once '../../models/CourseMode.php';
    $courseModel = new CourseMode(DB::getInstance());
    $sql = "SELECT c.* FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_user_id = ? ORDER BY c.created_at DESC";
    DB::getInstance()->query($sql, [$user->data()->id]);
    $courses = DB::getInstance()->results();
    ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Code</th>
                <th>Status</th>
                <th>Department</th>
                <th>Coordinator</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course->title); ?></td>
                <td><?php echo htmlspecialchars($course->code); ?></td>
                <td><?php echo htmlspecialchars($course->status); ?></td>
                <td><?php echo htmlspecialchars($course->department); ?></td>
                <td><?php echo htmlspecialchars($course->coordinator_user_id); ?></td>
                <td><?php echo htmlspecialchars($course->created_at); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
