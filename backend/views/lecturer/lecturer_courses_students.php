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
    <title>Enrolled Students</title>
</head>
<body>
    <h1>Enrolled Students</h1>
    <p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
    <!-- TODO: List students enrolled in lecturer's courses -->
</body>
</html>
