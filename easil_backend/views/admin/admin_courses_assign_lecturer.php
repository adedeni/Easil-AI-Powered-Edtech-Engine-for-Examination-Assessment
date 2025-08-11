<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) { echo '<p>No course selected.</p>'; exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_lecturer'])) {
    $lecturerId = $_POST['lecturer_user_id'];
    DB::getInstance()->insert('lecturer_courses', [
        'lecturer_user_id' => $lecturerId,
        'course_id' => $id
    ]);
    echo '<p>Lecturer assigned successfully!</p>';
}
$sql = "SELECT u.id, u.name FROM users u WHERE u.role_id = 2";
DB::getInstance()->query($sql);
$lecturers = DB::getInstance()->results();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Assign Lecturer</title></head>
<body>
<h1>Assign Lecturer to Course</h1>
<form method="post" action="">
    <select name="lecturer_user_id">
        <?php foreach ($lecturers as $lecturer): ?>
        <option value="<?php echo $lecturer->id; ?>"><?php echo htmlspecialchars($lecturer->name); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="assign_lecturer">Assign</button>
</form>
</body>
</html>
