<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
  
    require_once '../../app/Core/init.php';

    if(Input::exists()){
        Input::get('remember') ? 'Yes' : 'No';
        if(Token::check(Input::get('token'))) {
            $validate = new Validate();
            $validation = $validate->check($_POST, [
                'username' => ['required' => true],
                'password' => ['required' => true]
            ]);
            if($validation->passed()){
                $user = new User();
                $remember = (Input::get('remember') === 'on') ? true : false; 
                $login = $user->login(Input::get('username'), Input::get('password'), $remember);
                if($login){
                    // Ensure account is active
                    if (isset($user->data()->status) && $user->data()->status !== 'active') {
                        echo 'Account is inactive.';
                    } else if (isset($user->data()->force_password_change) && (int)$user->data()->force_password_change === 1) {
                        Redirect::to('changepassword.php');
                    } else {
                        // Determine role id from either role_id or roles
                        $roleId = null;
                        if (isset($user->data()->role_id)) {
                            $roleId = (int)$user->data()->role_id;
                        } elseif (isset($user->data()->roles)) {
                            $roleId = (int)$user->data()->roles;
                        }
                        // Redirect based on role
                        if ($roleId === 3) {
                            // Super admin route for id=1
                            if ((int)$user->data()->id === 1) {
                                Redirect::to('../admin/super_admin_dashboard.php');
                            } else {
                                Redirect::to('../admin/admin_dashboard.php');
                            }
                        } elseif ($roleId === 2) {
                            Redirect::to('../lecturer/lecturer_dashboard.php');
                        } else {
                            Redirect::to('../student/student_dashboard.php');
                        }
                    }
                }else{
                    echo "Login failed";
                }
            }else{
                foreach($validation->errors() as $error){
                    echo $error, '<br>';
                }
            }
        }
    }
    if(isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $token = $_POST['token'];
    }
    ?>
    <form action="" method="post">
       <div class="field">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" placeholder="Enter your username" required autocomplete="off" value="<?php echo escape(Input::get('username')); ?>">
       </div> <br>
       <div class="field">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required autocomplete="off">
       </div> <br>
       <div class="field">
       <label for="remember">
        <input type="checkbox" name="remember" id="remember"><span>   </span>Remember me
        </label>
       </div> <br>
       <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
       <input type="submit" name="login" value="Log in">
    </form> <br>
    <!-- Registration disabled -->
</body>
</html>