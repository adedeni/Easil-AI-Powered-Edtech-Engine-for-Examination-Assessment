<?php
require_once '../../app/Core/init.php';

$user = Guard::requireLogin();
if ((int)$user->data()->id !== 1) { 

    Redirect::to('../auth/login.php');
    Session::flash('Access Denied, you are not authorize to view this page');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
</head>
<body>
    <h1>Super Admin Dashboard</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <ul>
    <li><a href="admin_users.php">Manage Users</a></li>
    <li><a href="admin_courses.php">Manage Courses</a></li>
    <li><a href="admin_courses_import.php">Bulk Import Courses</a></li>
    <li><a href="admin_courses_enroll.php">Enroll Students in Courses</a></li>
    <li><a href="admin_audit_logs.php">View Audit Logs</a></li>
    <li><a href="../auth/changepassword.php">Change My Password</a></a></li>
    <li><a href="../profile.php?user=<?php echo escape($user->data()->username);?>">My Profile</a></li>
    <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</body>
</html>
