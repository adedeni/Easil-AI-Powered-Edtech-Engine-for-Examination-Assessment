<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body> 
    <h1>This is student admin page</h1>   
    <?php
    require_once 'core/init.php'; 
   
        


        if(Session::exists('success')){
            echo Session::flash('success');
        }
        $user = new User();
        if($user->isLoggedIn()){
            ?>
            <p>Welcome, <?php echo $user->data()->username; ?>!</p>
            <ul>
                <li><a href="profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo $user->data()->username; ?>'s Profile</a></li>
            <li><a href="update.php">Update Profile</a></li>
            <li><a href="changepassword.php">Change Password</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <?php
            if($user->hasPermission('moderator')){
                echo 'You are a moderator' . "!";
            } else {
                echo 'You are not a moderator'. "!";
            }
        }else{
            ?>
            <ul>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <?php
        } 
    ?>

</body>
</html>