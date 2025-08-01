
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
</head>
<body>
    <?php
        require_once 'core/init.php';

        $user = new User();
        if(!$user->isLoggedIn()){
            Redirect::to('login.php');
        }
        if(Input::exists()) {
            if(Token::check(Input::get('token'))){
                $validate = new Validate();
                $validation = $validate->check($_POST, [
                    'name' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 50
                    ]
                    ]);
                    if($validation->passed()){
                        try {
                            $user->update(['name' => Input::get('name')]);
                            Session::flash('home', 'Details Updated!');
                            Redirect::to('admin_dashboard.php');
                        } catch(Exception $e){
                            die($e->getMessage());
                        }
                    }
                }
        }
    ?> <br>
    <form action="" method="post">
        <div class="field">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo escape($user->data()->username); ?>" readonly>
        </div> <br>
        <div class="field">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo escape($user->data()->name); ?>">
        </div> <br>
        <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Update">
    </form>
</body>
</html>
