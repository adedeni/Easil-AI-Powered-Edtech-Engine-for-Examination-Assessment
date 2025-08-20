<?php
require_once '../../app/Core/init.php';
$user = new User();
if(!$user->isLoggedIn()){
    Redirect::to('../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>

<h1>Student Dashboard</h1>
<p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
<ul>
    <li><a href="../profile.php?user=<?php echo escape($user->data()->username);?>">Profile</a></li>
    <li><a href="../student/student_courses.php">My Courses</a></li>
    <li><a href="../student/student_courses_available.php">Available Courses</a></li>
    <li><a href="../auth/changepassword.php">Change Password</a></li>
    <li><a href="../auth/logout.php">Logout</a></li>
</ul>
</body>
</html>