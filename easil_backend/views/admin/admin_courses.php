<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
</head>
<body>
    <h1>Course Management</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <?php
    require_once '../../models/CourseMode.php';
    $courseModel = new CourseMode(DB::getInstance());
    $filters = [];
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
                <td><?php echo htmlspecialchars($course->coordinator); ?></td>
                <td><?php echo htmlspecialchars($course->created_at); ?></td>
                <td>
                    <a href="admin_courses_edit.php?id=<?php echo $course->id; ?>">Edit</a> |
                    <a href="admin_courses_delete.php?id=<?php echo $course->id; ?>" onclick="return confirm('Delete this course?');">Delete</a> |
                    <a href="admin_courses_details.php?id=<?php echo $course->id; ?>">Details</a> |
                    <a href="admin_courses_enrolled.php?id=<?php echo $course->id; ?>">Enrolled Students</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h2>Add New Course</h2>
    <form method="post" action="">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="code" placeholder="Code" required>
        <input type="text" name="description" placeholder="Description">
        <input type="text" name="department" placeholder="Department">
        <input type="text" name="coordinator_user_id" placeholder="Coordinator User ID">
        <input type="text" name="materials" placeholder="Materials (file path or description)">
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="submit" name="add_course">Add Course</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
        $fields = [
            'title' => $_POST['title'],
            'code' => $_POST['code'],
            'description' => $_POST['description'],
            'department' => $_POST['department'],
            'coordinator_user_id' => $_POST['coordinator_user_id'],
            'materials' => $_POST['materials'],
            'status' => $_POST['status'],
            'created_by_user_id' => $user->data()->id
        ];
        $courseModel->createCourse($fields);
        echo '<p>Course added successfully!</p>';
        echo '<meta http-equiv="refresh" content="1">';
    }
    ?>
    <a href="admin_courses_import.php">Bulk Import Courses</a>
    <a href="admin_courses_enroll.php">Enroll Students</a>
</body>
</html>
