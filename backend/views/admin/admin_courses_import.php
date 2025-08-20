<?php
require_once '../../app/Core/init.php';
$user = Guard::requireAdmin();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Import Courses</title>
</head>
<body>
    <h1>Bulk Import Courses</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <!-- TODO: Add bulk import form -->
</body>
</html>
