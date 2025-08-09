<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Super Admin Dashboard</title>
</head>
<body>
<?php
require_once 'core/init.php';
require_once 'classes/Guard.php';
$user = Guard::requireLogin();
if ((int)$user->data()->id !== 1) { die('Access denied'); }
?>
<h1>Super Admin Dashboard</h1>
<p>Welcome, <?php echo escape($user->data()->username); ?>!</p>
<ul>
  <li><a href="admin_users.php">Manage Users</a></li>
  <li><a href="admin_audit_logs.php">View Audit Logs</a></li>
  <li><a href="changepassword.php">Change My Password</a></li>
  <li><a href="profile.php?user=<?php echo escape($user->data()->username);?>">My Profile</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
</body>
</html>