<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
</head>
<body>
    <h1>Edit Course</h1>
    <?php
    require_once '../../models/CourseMode.php';
    $courseModel = new CourseMode(DB::getInstance());
    $coordinators = $courseModel->getCoordinators();
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$id) {
        echo '<p>No course selected.</p>'; 
        exit;
    }
    
    $course = $courseModel->getCourseDetails($id);
    
    // Check if a course was found with the given ID
    if (!$course) {
        echo '<p>Course not found.</p>';
        exit;
    }
    
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
        
        // Use a more robust check for success
        if ($courseModel->updateCourse($id, $fields)) {
            echo '<p>Course updated successfully!</p>';
            // Redirect to the same page to show updated values and prevent resubmission
            header("Location: admin_courses_edit.php?id=" . $id);
            exit;
        } else {
            echo '<p>Failed to update course.</p>';
        }
    }
    ?>
    
    <form method="post" action="">
    <input type="text" name="title" value="<?php echo htmlspecialchars($course->title); ?>" required>
    <input type="text" name="code" value="<?php echo htmlspecialchars($course->code); ?>" required>
    <input type="text" name="description" value="<?php echo htmlspecialchars($course->description); ?>">
    <input type="text" name="department" value="<?php echo htmlspecialchars($course->department); ?>">
    
    <select name="coordinator_user_id">
    <option value="">Select Coordinator</option>
    <?php foreach ($coordinators as $coordinator): ?>
        <option value="<?php echo htmlspecialchars($coordinator->id); ?>"
            <?php if ($coordinator->id == $course->coordinator_user_id) echo 'selected'; ?>>
            <?php echo htmlspecialchars($coordinator->username); ?>
        </option>
    <?php endforeach; ?>
</select>
    <input type="hidden" name="coordinator_user_id" value="<?php echo htmlspecialchars($course->coordinator_user_id); ?>">

    <input type="text" name="materials" value="<?php echo htmlspecialchars($course->materials); ?>">
    <select name="status">
        <option value="active" <?php if($course->status=='active')echo 'selected';?>>Active</option>
        <option value="inactive" <?php if($course->status=='inactive')echo 'selected';?>>Inactive</option>
    </select>
    <button type="submit" name="edit_course">Update Course</button>
</form>

    <a href="admin_courses.php">Back to Course Management</a>
</body>
</html>