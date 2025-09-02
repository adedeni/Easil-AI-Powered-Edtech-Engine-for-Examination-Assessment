<?php
// Users API Routes
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Get the specific action
$action = isset($requestUri[2]) ? $requestUri[2] : '';
$userId = isset($requestUri[3]) ? $requestUri[3] : null;

// Check authentication for all user operations
if (!Session::exists('user_id')) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "error" => "Authentication required"
    ]);
    exit();
}

// Check if user is admin for sensitive operations
$currentUser = new User();
$currentUserData = $currentUser->find(Session::get('user_id'));
$isAdmin = $currentUserData && $currentUserData->role_id == 1; // Assuming role_id 1 is admin

switch ($action) {
    case '':
        if ($requestMethod === 'GET') {
            handleGetUsers();
        } elseif ($requestMethod === 'POST' && $isAdmin) {
            handleCreateUser();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'create':
        if ($requestMethod === 'POST' && $isAdmin) {
            handleCreateUser();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'update':
        if ($requestMethod === 'PUT' && $isAdmin) {
            handleUpdateUser();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'delete':
        if ($requestMethod === 'DELETE' && $isAdmin) {
            handleDeleteUser();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'bulk-import':
        if ($requestMethod === 'POST' && $isAdmin) {
            handleBulkImportUsers();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'export':
        if ($requestMethod === 'GET' && $isAdmin) {
            handleExportUsers();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    case 'roles':
        if ($requestMethod === 'GET') {
            handleGetRoles();
        } else {
            http_response_code(405);
            echo json_encode(["success" => false, "error" => "Method not allowed"]);
        }
        break;

    default:
        if ($userId && $requestMethod === 'GET') {
            handleGetUser($userId);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "error" => "Users endpoint not found",
                "available_endpoints" => [
                    "" => "GET - List users, POST - Create user (admin only)",
                    "create" => "POST - Create user (admin only)",
                    "update" => "PUT - Update user (admin only)",
                    "delete" => "DELETE - Delete user (admin only)",
                    "bulk-import" => "POST - Bulk import users (admin only)",
                    "export" => "GET - Export users (admin only)",
                    "roles" => "GET - Get available roles",
                    "{id}" => "GET - Get specific user details"
                ]
            ]);
        }
        break;
}

function handleGetUsers() {
    $filters = [];
    
    // Get query parameters
    if (isset($_GET['role_id'])) $filters['role_id'] = $_GET['role_id'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['keywords'])) $filters['keywords'] = $_GET['keywords'];
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    
    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $userModel = new UserModel(DB::getInstance());
    $users = $userModel->getUsers($filters);
    
    // Apply pagination
    $totalUsers = count($users);
    $users = array_slice($users, $offset, $limit);
    
    echo json_encode([
        "success" => true,
        "data" => [
            "users" => $users,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $totalUsers,
                "pages" => ceil($totalUsers / $limit)
            ]
        ]
    ]);
}

function handleGetUser($userId) {
    $userModel = new UserModel(DB::getInstance());
    $user = $userModel->getUser($userId);
    
    if ($user) {
        echo json_encode([
            "success" => true,
            "data" => $user
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "User not found"
        ]);
    }
}

function handleCreateUser() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Required fields
    $requiredFields = ['username', 'name', 'email', 'role_id', 'identification_number'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "Field '$field' is required"
            ]);
            return;
        }
    }
    
    // Validate role_id
    if (!in_array($input['role_id'], [2, 3, 4])) { // 2=student, 3=lecturer, 4=admin
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Invalid role_id"
        ]);
        return;
    }
    
    // Check if username or email already exists
    $userModel = new UserModel(DB::getInstance());
    $existingUser = $userModel->getUserByUsername($input['username']);
    if ($existingUser) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Username already exists"
        ]);
        return;
    }
    
    // Generate default password (identification_number)
    $defaultPassword = Hash::make($input['identification_number']);
    
    $userData = [
        'username' => $input['username'],
        'name' => $input['name'],
        'email' => $input['email'],
        'role_id' => $input['role_id'],
        'identification_number' => $input['identification_number'],
        'password' => $defaultPassword,
        'force_password_change' => true,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Optional fields
    if (isset($input['department'])) $userData['department'] = $input['department'];
    if (isset($input['phone'])) $userData['phone'] = $input['phone'];
    
    if ($userModel->createUser($userData)) {
        echo json_encode([
            "success" => true,
            "message" => "User created successfully",
            "data" => [
                "username" => $input['username'],
                "default_password" => $input['identification_number']
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to create user"
        ]);
    }
}

function handleUpdateUser() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "User ID is required"
        ]);
        return;
    }
    
    $userId = $input['id'];
    $userModel = new UserModel(DB::getInstance());
    $existingUser = $userModel->getUser($userId);
    
    if (!$existingUser) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "User not found"
        ]);
        return;
    }
    
    // Allowed fields for update
    $allowedFields = ['name', 'email', 'department', 'phone', 'status', 'role_id'];
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
    
    if ($userModel->updateUser($userId, $updateData)) {
        echo json_encode([
            "success" => true,
            "message" => "User updated successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to update user"
        ]);
    }
}

function handleDeleteUser() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "User ID is required"
        ]);
        return;
    }
    
    $userId = $input['id'];
    $userModel = new UserModel(DB::getInstance());
    
    if ($userModel->deleteUser($userId)) {
        echo json_encode([
            "success" => true,
            "message" => "User deleted successfully"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Failed to delete user"
        ]);
    }
}

function handleBulkImportUsers() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($input['users']) || !is_array($input['users'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Users array is required"
        ]);
        return;
    }
    
    $userModel = new UserModel(DB::getInstance());
    $results = [];
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($input['users'] as $index => $userData) {
        try {
            // Validate required fields
            $requiredFields = ['username', 'name', 'email', 'role_id', 'identification_number'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($userData[$field]) || empty($userData[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                $results[] = [
                    "index" => $index,
                    "success" => false,
                    "error" => "Missing fields: " . implode(', ', $missingFields)
                ];
                $errorCount++;
                continue;
            }
            
            // Check if user already exists
            $existingUser = $userModel->getUserByUsername($userData['username']);
            if ($existingUser) {
                $results[] = [
                    "index" => $index,
                    "success" => false,
                    "error" => "Username already exists"
                ];
                $errorCount++;
                continue;
            }
            
            // Create user
            $defaultPassword = Hash::make($userData['identification_number']);
            $userData['password'] = $defaultPassword;
            $userData['force_password_change'] = true;
            $userData['status'] = 'active';
            $userData['created_at'] = date('Y-m-d H:i:s');
            
            if ($userModel->createUser($userData)) {
                $results[] = [
                    "index" => $index,
                    "success" => true,
                    "message" => "User created successfully"
                ];
                $successCount++;
            } else {
                $results[] = [
                    "index" => $index,
                    "success" => false,
                    "error" => "Failed to create user"
                ];
                $errorCount++;
            }
            
        } catch (Exception $e) {
            $results[] = [
                "index" => $index,
                "success" => false,
                "error" => "Exception: " . $e->getMessage()
            ];
            $errorCount++;
        }
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Bulk import completed",
        "data" => [
            "total" => count($input['users']),
            "successful" => $successCount,
            "failed" => $errorCount,
            "results" => $results
        ]
    ]);
}

function handleExportUsers() {
    $filters = [];
    
    if (isset($_GET['role_id'])) $filters['role_id'] = $_GET['role_id'];
    if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
    if (isset($_GET['department'])) $filters['department'] = $_GET['department'];
    
    $userModel = new UserModel(DB::getInstance());
    $users = $userModel->getUsers($filters);
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, ['ID', 'Username', 'Name', 'Email', 'Role', 'Department', 'Status', 'Created At']);
    
    // CSV data
    foreach ($users as $user) {
        $role = $user->role_id == 2 ? 'Student' : ($user->role_id == 3 ? 'Lecturer' : 'Admin');
        fputcsv($output, [
            $user->id,
            $user->username,
            $user->name,
            $user->email,
            $role,
            $user->department ?? '',
            $user->status,
            $user->created_at
        ]);
    }
    
    fclose($output);
    exit();
}

function handleGetRoles() {
    $userModel = new UserModel(DB::getInstance());
    $roles = $userModel->getRoles();
    
    echo json_encode([
        "success" => true,
        "data" => $roles
    ]);
}
