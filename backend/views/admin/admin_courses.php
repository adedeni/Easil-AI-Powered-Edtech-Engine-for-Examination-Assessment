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
        <td><?php echo htmlspecialchars($course->title ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($course->code ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($course->status ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($course->department ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($course->coordinator ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($course->created_at ?? 'N/A'); ?></td>
        <td>
            <a href="admin_courses_edit.php?id=<?php echo htmlspecialchars($course->id); ?>">Edit</a> |
            <a href="admin_courses_delete.php?id=<?php echo htmlspecialchars($course->id); ?>" onclick="return confirm('Delete this course?');">Delete</a> |
            <a href="admin_courses_details.php?id=<?php echo htmlspecialchars($course->id); ?>">Details</a> |
            <a href="admin_courses_enrolled.php?id=<?php echo htmlspecialchars($course->id); ?>">Enrolled Students</a>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
    </table>
    <h2>Add New Course</h2>
    <form method="post" action="add_course_process.php">
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
   
    <a href="admin_courses_import.php">Bulk Import Courses</a>
    <a href="admin_courses_enrolled.php?course_id=<?php echo $course['id']; ?>">Enroll Students</a>
    <p>
        <?php
        require_once '../../app/Core/constants.php';
        $dashboardLink = ($user->data()->id === $SUPER_ADMIN_ID) ? 'super_admin_dashboard.php' : 'admin_dashboard.php';
        ?>
        <a href="<?php echo $dashboardLink; ?>">Back to Dashboard</a>
    </p>
</body>
</html>