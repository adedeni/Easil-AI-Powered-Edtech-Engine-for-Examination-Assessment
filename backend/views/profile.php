<?php
    require_once '../app/Core/init.php';


    $loggedInUser = Guard::requireLogin();

    if (!$username = Input::get('user')) {

        Redirect::to('profile.php?user=' . $loggedInUser->data()->username);
    } else {
        $user = new User($username);
        if (!$user->exists()) {

            Redirect::to(404);
        } else {
            $data = $user->data();
        }
    }

    // Fetch role name
    $roleName = '';
    if (isset($data->role_id)) {
        $db = DB::getInstance();
        $role = $db->get('roles', ['id', '=', $data->role_id])->first();
        if ($role) {
            $roleName = ucfirst(strtolower($role->name));
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
</head>

<body>
    <h1>Profile Page</h1>
    
    <h3><?php echo escape($data->username); ?></h3>
    <p>Full name: <?php echo escape($data->name); ?></p>
    <p>Email: <?php echo escape($data->email); ?></p>
    <?php if (in_array(strtolower($roleName), ['student', 'lecturer'])): ?>
        <p>Department: <?php echo !empty($data->department) ? escape($data->department) : 'Not specified'; ?></p>
    <?php else: ?>
        <p>Department: Not applicable</p>
    <?php endif; ?>
    <?php if (!empty($data->phone)): ?>
        <p>Phone: <?php echo escape($data->phone); ?></p>
    <?php endif; ?>
    <?php if (!empty($data->created_at)): ?>
        <p>Joined: <?php echo escape($data->created_at); ?></p>
    <?php endif; ?>
    <p>Role/Position: <?php echo $roleName ? escape($roleName) : 'Unknown'; ?></p>
</body>

</html>