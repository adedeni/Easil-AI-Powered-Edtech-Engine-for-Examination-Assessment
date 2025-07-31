<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h1>Change Password</h1>
    <?php
    require_once 'core/init.php';

    $user = new User();
    if(!$user->isLoggedIn()){
        Redirect::to('index.php');
    }
    if(Input::exists()){
        if(Token::check(Input::get('token'))){
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'password_current' => [
                    'required' => true,
                    'min' => 6
                ],
                'password_new' => [
                    'required' => true,
                    'min' => 6
                ],
                'password_new_confirm' => [
                    'required' => true,
                    'min' => 6,
                    'matches' => 'password_new',
                ],
            ]);
            if($validation->passed()){
                if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password){
                    echo 'Current password is incorrect';
                }else{
                    $salt = Hash::salt(32);
                    $user->update([
                        'password' => Hash::make(Input::get('password_new'), $salt), 
                        'salt' => $salt
                    ]);
                    Session::flash('home', 'Password updated!');
                    Redirect::to('index.php');
                }
            }else{
                foreach($validation->errors() as $error){
                    echo $error, '<br>';
                }
            }
        }
    }
    ?>
    <form action="" method="post">
        <div class="field"> <br>
            <label for="password_current">Current Password</label>
            <input type="password" name="password_current" id="password_current">
        </div> <br>
        <div class="field">
            <label for="password_new">New Password</label>
            <input type="password" name="password_new" id="password_new">
        </div> <br>
        <div class="field">
            <label for="password_new_confirm">Confirm New Password</label>
            <input type="password" name="password_new_confirm" id="password_new_confirm">
        </div> <br>
        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Change Password">
    </form>
</body>
</html>