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
    <title>Enroll Students</title>
</head>
<body>
    <h1>Enroll Students</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <?php
    require_once '../../models/CourseMode.php';
    $courseModel = new CourseMode(DB::getInstance());
    $courseId = isset($_GET['course_id']) ? $_GET['course_id'] : null;
    if (!$courseId) {
        echo '<p>No course selected.</p>';
    } else {
        // List students not yet enrolled in this course
        $sql = "SELECT u.id, u.name, u.username FROM users u WHERE u.role_id = 1 AND u.id NOT IN (SELECT e.student_user_id FROM enrollments e WHERE e.course_id = ?)";
        DB::getInstance()->query($sql, [$courseId]);
        $students = DB::getInstance()->results();
        ?>
        <form method="post" action="">
            <input type="hidden" name="course_id" value="<?php echo $courseId; ?>">
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Name</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><input type="checkbox" name="student_ids[]" value="<?php echo $student->id; ?>"></td>
                        <td><?php echo htmlspecialchars($student->name); ?></td>
                        <td><?php echo htmlspecialchars($student->username); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" name="enroll">Enroll Selected Students</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll']) && !empty($_POST['student_ids'])) {
            $studentIds = $_POST['student_ids'];
            foreach ($studentIds as $studentId) {
                DB::getInstance()->insert('enrollments', [
                    'student_user_id' => $studentId,
                    'course_id' => $courseId
                ]);
            }
            echo '<p>Enrollment successful!</p>';
            echo '<meta http-equiv="refresh" content="1">';
        }
    }
    ?>
</body>
</html>
