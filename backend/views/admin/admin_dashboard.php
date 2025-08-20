<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body> 
    <h1>This is Easil Admin Dashboard</h1>   
    <?php
    require_once '../../app/Core/init.php';
    
    $user = Guard::requireAdmin();
        if(Session::exists('success')){
            echo Session::flash('success');
        }
            ?>
            <p>Welcome, <?php echo $user->data()->username; ?>!</p>
            <ul>
                <li><a href="../profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo $user->data()->username; ?>'s Profile</a></li>
            <li><a href="../update.php">Update Profile</a></li>
            <li><a href="../auth/changepassword.php">Change Password</a></li>
                
                <li><a href="../admin/admin_users.php">Manage Users</a></li>
                <li><a href="../admin/admin_courses.php">Manage Courses</a></li>
                <li><a href="../admin/admin_courses_import.php">Bulk Import Courses</a></li>

                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
</body>
</html>