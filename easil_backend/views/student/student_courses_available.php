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
    <title>Available Courses</title>
</head>
<body>
    <h1>Available Courses</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <?php
    require_once '../../models/CourseMode.php';
    $courseModel = new CourseMode(DB::getInstance());
    // Get courses not yet enrolled by this student
    $sql = "SELECT c.* FROM courses c WHERE c.id NOT IN (SELECT e.course_id FROM enrollments e WHERE e.student_user_id = ?) AND c.status = 'active' ORDER BY c.created_at DESC";
    DB::getInstance()->query($sql, [$user->data()->id]);
    $courses = DB::getInstance()->results();
    ?>
    <form method="get" action="">
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
    <?php
    // Apply filters
    $filters = [];
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    if (isset($_GET['title'])) $filters['title'] = $_GET['title'];
    if (isset($_GET['code'])) $filters['code'] = $_GET['code'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['date'])) $filters['date'] = $_GET['date'];
    $courseModel = new CourseMode(DB::getInstance());
    $allCourses = $courseModel->getCourses($filters);
    // Filter out already enrolled
    $sql = "SELECT course_id FROM enrollments WHERE student_user_id = ?";
    DB::getInstance()->query($sql, [$user->data()->id]);
    $enrolledIds = array_map(function($row){return $row->course_id;}, DB::getInstance()->results());
    $courses = array_filter($allCourses, function($course) use ($enrolledIds) { return !in_array($course->id, $enrolledIds) && $course->status == 'active'; });
    ?>
    <form method="post" action="">
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Select</th>
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
                    <td><input type="checkbox" name="course_ids[]" value="<?php echo $course->id; ?>"></td>
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
        <button type="submit" name="enroll">Enroll Selected Courses</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll']) && !empty($_POST['course_ids'])) {
        $courseIds = $_POST['course_ids'];
        foreach ($courseIds as $courseId) {
            DB::getInstance()->insert('enrollments', [
                'student_user_id' => $user->data()->id,
                'course_id' => $courseId
            ]);
        }
        echo '<p>Enrollment successful!</p>';
        echo '<meta http-equiv="refresh" content="1">';
    }
    ?>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll']) && !empty($_POST['course_ids'])) {
        $courseIds = $_POST['course_ids'];
        foreach ($courseIds as $courseId) {
            DB::getInstance()->insert('enrollments', [
                'student_user_id' => $user->data()->id,
                'course_id' => $courseId
            ]);
        }
        echo '<p>Enrollment successful!</p>';
        echo '<meta http-equiv="refresh" content="1">';
    }
    
    ?>
</body>
</html>
