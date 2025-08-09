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
        Redirect::to('login.php');
    }

    $isForced = (isset($user->data()->force_password_change) && (int)$user->data()->force_password_change === 1);

    if(Input::exists()){
        if(Token::check(Input::get('token'))){
            $validate = new Validate();
            $rules = [
                'password_new' => [
                    'required' => true,
                    'min' => 6
                ],
                'password_new_confirm' => [
                    'required' => true,
                    'min' => 6,
                    'matches' => 'password_new',
                ],
            ];
            if (!$isForced) {
                $rules['password_current'] = [
                    'required' => true,
                    'min' => 6
                ];
            }
            $validation = $validate->check($_POST, $rules);

            if($validation->passed()){
                if(!$isForced) {
                    if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password){
                        echo 'Current password is incorrect';
                    } else {
                        $salt = Hash::salt(32);
                        $user->update([
                            'password' => Hash::make(Input::get('password_new'), $salt), 
                            'salt' => $salt,
                            'force_password_change' => 0,
                            'last_password_change' => date('Y-m-d H:i:s')
                        ]);
                        Session::flash('success', 'Password updated!');
                        // Redirect based on role
                        $roleId = isset($user->data()->role_id) ? (int)$user->data()->role_id : (int)$user->data()->roles;
                        if ($roleId === 3) {
                            Redirect::to('admin_dashboard.php');
                        } elseif ($roleId === 2) {
                            Redirect::to('lecturer_dashboard.php');
                        } else {
                            Redirect::to('student_dashboard.php');
                        }
                    }
                } else {
                    $salt = Hash::salt(32);
                    $user->update([
                        'password' => Hash::make(Input::get('password_new'), $salt), 
                        'salt' => $salt,
                        'force_password_change' => 0,
                        'last_password_change' => date('Y-m-d H:i:s')
                    ]);
                    // Invalidate remember-me sessions for this user after password change
                    DB::getInstance()->delete('users_session', ['users_id', '=', $user->data()->id]);
                    Session::flash('success', 'Password updated!');
                    $roleId = isset($user->data()->role_id) ? (int)$user->data()->role_id : (int)$user->data()->roles;
                    if ($roleId === 3) {
                        Redirect::to('admin_dashboard.php');
                    } elseif ($roleId === 2) {
                        Redirect::to('lecturer_dashboard.php');
                    } else {
                        Redirect::to('student_dashboard.php');
                    }
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
        <?php if(!$isForced): ?>
        <div class="field"> <br>
            <label for="password_current">Current Password</label>
            <input type="password" name="password_current" id="password_current">
        </div> <br>
        <?php else: ?>
        <p>You must set a new password before proceeding.</p>
        <?php endif; ?>
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