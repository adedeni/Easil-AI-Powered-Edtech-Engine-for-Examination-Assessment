<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Students in Courses</title>
</head>
<body>
    <h1>Enroll Students in Courses</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <!-- TODO: Add enrollment form (individual/bulk) -->
</body>
</html>
