<?php
require_once '../../app/Core/init.php';
$user = new User();
$user->logout();
Redirect::to('login.php');
Session::flash('success', 'You have been logged out');