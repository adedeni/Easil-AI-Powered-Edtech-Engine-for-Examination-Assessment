<?php
require_once '../../app/Core/init.php';
require_once '../../models/CourseMode.php';
$user = Guard::requireAdmin();
$courseModel = new CourseMode(DB::getInstance());
$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) { echo '<p>No course selected.</p>'; exit; }
$course = $courseModel->getCourseDetails($id);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_course'])) {
    $fields = [
        'title' => $_POST['title'],
        'code' => $_POST['code'],
        'description' => $_POST['description'],
        'department' => $_POST['department'],
        'coordinator_user_id' => $_POST['coordinator_user_id'],
        'materials' => $_POST['materials'],
        'status' => $_POST['status']
    ];
    $courseModel->updateCourse($id, $fields);
    echo '<p>Course updated successfully!</p>';
    echo '<meta http-equiv="refresh" content="1">';
    $course = $courseModel->getCourseDetails($id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Course</title></head>
<body>
<h1>Edit Course</h1>
<form method="post" action="">
    <input type="text" name="title" value="<?php echo htmlspecialchars($course->title); ?>" required>
    <input type="text" name="code" value="<?php echo htmlspecialchars($course->code); ?>" required>
    <input type="text" name="description" value="<?php echo htmlspecialchars($course->description); ?>">
    <input type="text" name="department" value="<?php echo htmlspecialchars($course->department); ?>">
    <input type="text" name="coordinator_user_id" value="<?php echo htmlspecialchars($course->coordinator_user_id); ?>">
    <input type="text" name="materials" value="<?php echo htmlspecialchars($course->materials); ?>">
    <select name="status">
        <option value="active" <?php if($course->status=='active')echo 'selected';?>>Active</option>
        <option value="inactive" <?php if($course->status=='inactive')echo 'selected';?>>Inactive</option>
    </select>
    <button type="submit" name="edit_course">Update Course</button>
</form>
</body>
</html>
