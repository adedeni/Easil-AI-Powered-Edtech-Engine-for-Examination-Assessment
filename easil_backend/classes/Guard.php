<?php
class Guard {
    public static function requireLogin(): User {
        require_once __DIR__ . '/../core/init.php';
        $u = new User();
        if (!$u->isLoggedIn()) {
            Redirect::to('login.php');
        }
        return $u;
    }

    public static function requireAdmin(): User {
        $u = self::requireLogin();
        $roleId = Session::exists('user_role_id') ? (int)Session::get('user_role_id') : ($u->getRoleId() ?? 0);
        if ($roleId !== 3) {
            die('Access denied');
        }
        return $u;
    }
}