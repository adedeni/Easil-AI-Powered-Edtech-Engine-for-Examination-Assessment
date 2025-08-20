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
    $filters = ['lecturer_user_id' => $user->data()->id];
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    if (isset($_GET['title'])) $filters['title'] = $_GET['title'];
    if (isset($_GET['code'])) $filters['code'] = $_GET['code'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['date'])) $filters['date'] = $_GET['date'];
    $courses = $courseModel->getCourses($filters);
    ?>
    <form method="get">
        <input type="text" name="keywords" placeholder="Search keywords">
        <input type="text" name="department" placeholder="Department">
        <input type="text" name="title" placeholder="Title">
        <input type="text" name="code" placeholder="Code">
        <select name="status">
            <option value="">Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <input type="date" name="date" placeholder="Date">
        <button type="submit">Filter</button>
    </form>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Title</th>
                <th>Code</th>
                <th>Status</th>
                <th>Department</th>
                <th>Coordinator</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course->title); ?></td>
                <td><?php echo htmlspecialchars($course->code); ?></td>
                <td><?php echo htmlspecialchars($course->status); ?></td>
                <td><?php echo htmlspecialchars($course->department); ?></td>
                <td><?php echo htmlspecialchars($course->coordinator ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($course->created_at); ?></td>
                <td>
                    <a href="lecturer_courses_enroll.php?course_id=<?php echo $course->id; ?>">Enroll Students</a> |
                    <a href="lecturer_courses_students.php?course_id=<?php echo $course->id; ?>">View Enrolled Students</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>