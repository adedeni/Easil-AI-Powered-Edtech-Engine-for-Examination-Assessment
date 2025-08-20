<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('APPROOT', dirname(dirname(__DIR__)));

$GLOBALS['config'] = [
    'mysql' => [
        'host' => 'localhost',
        'username' => 'cereuste',
        'password' => '2-hmNTOVo!965t',
        'db' => 'cereuste_easil'
    ],
    'remember' => [
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ],
    'session' => [
        'session_name' => 'user',
        'token_name' => 'token'
    ],
    'captcha' => [
        'failed_login_attempts_before_captcha' => 3
    ]
];

spl_autoload_register(function ($class) {
    require_once APPROOT . '/app/Classes/' . $class . '.php';
});

require_once APPROOT . '/app/Functions/sanitize.php';

if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', ['hash', '=', $hash]);
    if ($hashCheck->count()) {
        $user = new User($hashCheck->first()->users_id);
        $user->login();
    }
}
