<?php
require_once '../../app/Core/init.php';
$user = new User();
if(!$user->isLoggedIn()){
    Redirect::to('../auth/login.php');
}
require_once '../../app/Models/EnrollmentModel.php';
require_once '../../app/Models/CourseModel.php';

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
if ($course_id <= 0) {
    echo "<p style='color:red;'>No course selected or invalid course ID.</p>";
    exit;
}
$course = CourseModel::find($course_id);
if (!$course) {
    echo "<p style='color:red;'>Course not found.</p>";
    exit;
}
// Check if lecturer is assigned to this course (many-to-many)
$db = DB::getInstance();
$assigned = $db->query(
    "SELECT * FROM lecturer_courses WHERE lecturer_user_id = ? AND course_id = ?",
    [$user->data()->id, $course_id]
)->count();
if (!$assigned) {
    echo "<p style='color:red;'>You are not assigned to this course.</p>";
    exit;
}

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$students = EnrollmentModel::getEnrolledStudents($course_id, $keyword);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrolled Students - <?php echo htmlspecialchars($course->title); ?></title>
</head>
<body>
    <h1>Enrolled Students for <?php echo htmlspecialchars($course->title); ?></h1>
    <p><a href="lecturer_courses.php">&larr; Back to My Courses</a></p>
    <form method="get" action="lecturer_courses_students.php">
        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
        <input type="text" name="keyword" placeholder="Search by name or email" value="<?php echo htmlspecialchars($keyword); ?>">
        <button type="submit">Filter</button>
    </form>
    <table border="1" cellpadding="5">
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Date Enrolled</th>
        </tr>
        <?php foreach ($students as $i => $student): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($student->student_id); ?></td>
            <td><?php echo htmlspecialchars($student->name); ?></td>
            <td><?php echo htmlspecialchars($student->email); ?></td>
            <td><?php echo htmlspecialchars($student->enrolled_at); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
