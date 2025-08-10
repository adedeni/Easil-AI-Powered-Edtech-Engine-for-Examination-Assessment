<?php
class User
{
    private $_db,
        $_data,
        $_sessionName,
        $_cookieName,
        $_isLoggedIn;

    // Lockout policy
    private int $maxFailedAttempts = 5;
    private int $lockMinutes = 15;

    public function __construct($user = null)
    {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');
        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);
                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                } else {
                    $this->logout();
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function update($fields = [], $id = null)
    {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }
        if (!$this->_db->update('users', $id, $fields)) {
            throw new Exception('There was a problem updating');
        }
    }

    public function create($fields = [])
    {
        try {

            if (!$this->_db->insert('users', $fields)) {
                $dbError = $this->_db->getError();
                throw new Exception('Database Error: ' . $dbError);
            }
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getLastError()
    {
        return $this->_db->getError();
    }

    public function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users', [$field, '=', $user]);
            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    private function isCurrentlyLocked(): bool
    {
        if (isset($this->data()->lock_until) && $this->data()->lock_until) {
            $now = new DateTimeImmutable('now');
            $lockUntil = new DateTimeImmutable($this->data()->lock_until);
            return $lockUntil > $now;
        }
        return false;
    }

    private function resolveRoleId(): ?int
    {
        if (isset($this->data()->role_id)) {
            return (int)$this->data()->role_id;
        }
        if (isset($this->data()->roles)) {
            return (int)$this->data()->roles;
        }
        return null;
    }

    public function login($username = null, $password = null, $remember = false)
    {
        // Check if user is already logged in (e.g., via session)
        if (!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->id);
            $rid = $this->resolveRoleId();
            if ($rid !== null) {
                Session::put('user_role_id', $rid);
            }
            return true;
        }
    
        // Find the user by username
        $user = $this->find($username);
    
        // If user is found, proceed with validation
        if ($user) {
            // Check for inactive status
            if (isset($this->data()->status) && $this->data()->status !== 'active') {
                return false;
            }
            
            // Check for an active lockout
            if ($this->isCurrentlyLocked()) {
                return false;
            }
    
            // Successful login
            if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
                // Reset failed attempts and lock_until on successful login
                $resetFields = [];
                if (property_exists($this->data(), 'failed_login_attempts')) {
                    $resetFields['failed_login_attempts'] = 0;
                }
                if (property_exists($this->data(), 'lock_until')) {
                    $resetFields['lock_until'] = null;
                }
                if (!empty($resetFields)) {
                    try {
                        $this->_db->update('users', $this->data()->id, $resetFields);
                    } catch (Exception $e) {
                        // Log or handle this exception silently as it shouldn't block the login
                    }
                }
    
                // Set session and role ID
                Session::put($this->_sessionName, $this->data()->id);
                $rid = $this->resolveRoleId();
                if ($rid !== null) {
                    Session::put('user_role_id', $rid);
                }
    
                // Handle "remember me" functionality
                if ($remember) {
                    $hash = Hash::unique();
                    $hashCheck = $this->_db->get('users_session', ['users_id', '=', $this->data()->id]);
                    if (!$hashCheck->count()) {
                        $this->_db->insert('users_session', [
                            'users_id' => $this->data()->id,
                            'hash' => $hash
                        ]);
                    } else {
                        $hash = $hashCheck->first()->hash;
                    }
                    Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
                }
                return true;
            }
    
            // Failed login attempt
            else {
                // Increment failed attempts and maybe lock the account
                $failed = 0;
                if (isset($this->data()->failed_login_attempts)) {
                    $failed = (int)$this->data()->failed_login_attempts;
                }
                $failed++;
                $update = ['failed_login_attempts' => $failed];
    
                // If max attempts reached, set the lock_until timestamp
                if ($failed >= $this->maxFailedAttempts) {
                    $lockUntil = (new DateTimeImmutable('now'))->modify('+' . $this->lockMinutes . ' minutes');
                    $update['lock_until'] = $lockUntil->format('Y-m-d H:i:s');
                }
    
                // Update the database
                try {
                    $this->_db->update('users', $this->data()->id, $update);
                } catch (Exception $e) {
                    // Log or handle this exception silently
                }
            }
        }
    
        // Return false for any failure to find user or authenticate
        return false;
    }

    public function hasPermission($key)
    {
        // Support either role_id or roles column, and permissions/permission fields
        $roleId = $this->resolveRoleId();
        if ($roleId === null) {
            return false;
        }
        $role = $this->_db->get('roles', ['id', '=', $roleId]);
        if ($role->count()) {
            $roleRow = $role->first();
            $permissionsJson = null;
            if (isset($roleRow->permissions)) {
                $permissionsJson = $roleRow->permissions;
            } elseif (isset($roleRow->permission)) {
                $permissionsJson = $roleRow->permission;
            }
            if ($permissionsJson) {
                $permissions = is_string($permissionsJson) ? json_decode($permissionsJson, true) : $permissionsJson;
                if (is_array($permissions) && !empty($permissions[$key])) {
                    return true;
                }
            }
        }
        return false;
    }

    // Helpers
    public function getRoleId(): ?int
    {
        return $this->resolveRoleId();
    }

    public function getRoleName(): ?string
    {
        $rid = $this->resolveRoleId();
        if ($rid === null) {
            return null;
        }
        $role = $this->_db->get('roles', ['id', '=', $rid]);
        if ($role->count()) {
            return $role->first()->name ?? null;
        }
        return null;
    }

    public function getIdentificationNumber(): ?string
    {
        return isset($this->data()->identification_number) ? $this->data()->identification_number : null;
    }
    public function generateRandomPassword($length = 12)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@$!%*?&';
        $pwd = '';
        for ($i = 0; $i < $length; $i++) {
            $pwd .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $pwd;
    }

    public function exists()
    {
        return !empty($this->_data);
    }
    public function logout()
    {
        $this->_db->delete('users_session', ['users_id', '=', $this->data()->id]);
        Session::delete($this->_sessionName);
        Session::delete('user_role_id');
        Cookie::delete($this->_cookieName);
        Session::delete('success');
    }
    public function data()
    {
        return $this->_data;
    }
    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }
}
