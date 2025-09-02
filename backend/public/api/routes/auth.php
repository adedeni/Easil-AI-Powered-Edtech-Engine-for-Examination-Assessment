<?php
// Authentication API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific auth action
$action = isset($requestUri[2]) ? $requestUri[2] : '';

switch ($action) {
    case 'login':
        if ($requestMethod === 'POST') {
            handleLogin();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'logout':
        if ($requestMethod === 'POST') {
            handleLogout();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'profile':
        if ($requestMethod === 'GET') {
            handleGetProfile();
        } elseif ($requestMethod === 'PUT') {
            handleUpdateProfile();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'change-password':
        if ($requestMethod === 'POST') {
            handleChangePassword();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "success" => false, 
            "error" => "Auth endpoint not found",
            "available_endpoints" => [
                "login" => "POST - User login",
                "logout" => "POST - User logout", 
                "profile" => "GET/PUT - User profile",
                "change-password" => "POST - Change password"
            ]
        ]);
        break;
}

function handleLogin() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Username and password are required"
        ]);
        return;
    }

    $username = $input['username'];
    $password = $input['password'];

    $user = new User();
    if ($user->login($username, $password)) {
        $userData = $user->data();
        
        // Get role information
        $role = $user->getRole();
        
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "user" => [
                "id" => $userData->id,
                "username" => $userData->username,
                "name" => $userData->name,
                "email" => $userData->email,
                "role_id" => $userData->role_id,
                "role_name" => $role ? $role->name : null,
                "identification_number" => $userData->identification_number,
                "force_password_change" => $userData->force_password_change ?? false,
                "status" => $userData->status
            ],
            "token" => generateAuthToken($userData->id)
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Invalid username or password"
        ]);
    }
}

function handleLogout() {
    // Clear session
    Session::delete('user_id');
    Session::delete('user_role');
    
    echo json_encode([
        "success" => true,
        "message" => "Logout successful"
    ]);
}

function handleGetProfile() {
    // Check if user is authenticated
    if (!Session::exists('user_id')) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Authentication required"
        ]);
        return;
    }

    $userId = Session::get('user_id');
    $user = new User();
    $userData = $user->find($userId);
    
    if ($userData) {
        $role = $user->getRole();
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $userData->id,
                "username" => $userData->username,
                "name" => $userData->name,
                "email" => $userData->email,
                "role_id" => $userData->role_id,
                "role_name" => $role ? $role->name : null,
                "identification_number" => $userData->identification_number,
                "status" => $userData->status,
                "created_at" => $userData->created_at
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "User not found"
        ]);
    }
}

function handleUpdateProfile() {
    if (!Session::exists('user_id')) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Authentication required"
        ]);
        return;
    }

    $userId = Session::get('user_id');
    $input = json_decode(file_get_contents("php://input"), true);
    
    $allowedFields = ['name', 'email'];
    $updateData = [];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateData[$field] = $input[$field];
        }
    }
    
    if (empty($updateData)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "No valid fields to update"
        ]);
        return;
    }
    
    $user = new User();
    if ($user->update($userId, $updateData)) {
        echo json_encode([
            "success" => true,
            "message" => "Profile updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update profile"
        ]);
    }
}

function handleChangePassword() {
    if (!Session::exists('user_id')) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "error" => "Authentication required"
        ]);
        return;
    }

    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['current_password']) || !isset($input['new_password'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Current password and new password are required"
        ]);
        return;
    }
    
    $userId = Session::get('user_id');
    $currentPassword = $input['current_password'];
    $newPassword = $input['new_password'];
    
    $user = new User();
    $userData = $user->find($userId);
    
    if (!$userData) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "User not found"
        ]);
        return;
    }
    
    // Verify current password
    if (!Hash::verify($currentPassword, $userData->password)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Current password is incorrect"
        ]);
        return;
    }
    
    // Update password
    $hashedPassword = Hash::make($newPassword);
    if ($user->update($userId, [
        'password' => $hashedPassword,
        'force_password_change' => false
    ])) {
        echo json_encode([
            "success" => true,
            "message" => "Password changed successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to change password"
        ]);
    }
}

function generateAuthToken($userId) {
    // Simple token generation - you might want to use JWT in production
    return base64_encode($userId . '_' . time() . '_' . rand(1000, 9999));
}
