<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'core/init.php';

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));
$endpoint = end($path_parts);

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    switch ($endpoint) {
        case 'register':
            if ($request_method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    throw new Exception('Invalid JSON data');
                }
                
                $validate = new Validate();
                $validation = $validate->check($input, [
                    'name' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 50
                    ],
                    'username' => [
                        'required' => true,
                        'min' => 2,
                        'max' => 20,
                        'unique' => 'users'
                    ],
                    'password' => [
                        'required' => true,
                        'min' => 6,
                    ]
                ]);
                
                if ($validation->passed()) {
                    $user = new User();
                    $salt = Hash::salt(32);
                    
                    $userData = [
                        'name' => $input['name'],
                        'username' => $input['username'],
                        'password' => Hash::make($input['password'], $salt),
                        'salt' => $salt,
                        'created_at' => date('Y-m-d H:i:s'),
                        'roles' => 1
                    ];
                    
                    $user->create($userData);
                    
                    $response = [
                        'success' => true,
                        'message' => 'User registered successfully',
                        'data' => [
                            'username' => $input['username'],
                            'name' => $input['name']
                        ]
                    ];
                } else {
                    $response['message'] = 'Validation failed';
                    $response['errors'] = $validation->errors();
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        case 'login':
            if ($request_method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    throw new Exception('Invalid JSON data');
                }
                
                $validate = new Validate();
                $validation = $validate->check($input, [
                    'username' => ['required' => true],
                    'password' => ['required' => true]
                ]);
                
                if ($validation->passed()) {
                    $user = new User();
                    $login = $user->login($input['username'], $input['password'], isset($input['remember']) ? $input['remember'] : false);
                    
                    if ($login) {
                        $userData = $user->data();
                        $response = [
                            'success' => true,
                            'message' => 'Login successful',
                            'data' => [
                                'id' => $userData->id,
                                'username' => $userData->username,
                                'name' => $userData->name,
                                'created_at' => $userData->createdAt,
                                'isLoggedIn' => true
                            ]
                        ];
                    } else {
                        $response['message'] = 'Invalid username or password';
                    }
                } else {
                    $response['message'] = 'Validation failed';
                    $response['errors'] = $validation->errors();
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        case 'logout':
            if ($request_method === 'POST') {
                $user = new User();
                if ($user->isLoggedIn()) {
                    $user->logout();
                    $response = [
                        'success' => true,
                        'message' => 'Logged out successfully'
                    ];
                } else {
                    $response['message'] = 'Not logged in';
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        case 'profile':
            if ($request_method === 'GET') {
                $user = new User();
                if ($user->isLoggedIn()) {
                    $userData = $user->data();
                    $response = [
                        'success' => true,
                        'data' => [
                            'id' => $userData->id,
                            'username' => $userData->username,
                            'name' => $userData->name,
                            'created_at' => $userData->createdAt,
                            'isLoggedIn' => true
                        ]
                    ];
                } else {
                    $response['message'] = 'Not logged in';
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        case 'update':
            if ($request_method === 'PUT') {
                $user = new User();
                if ($user->isLoggedIn()) {
                    $input = json_decode(file_get_contents('php://input'), true);
                    
                    if (!$input) {
                        throw new Exception('Invalid JSON data');
                    }
                    
                    $validate = new Validate();
                    $validation = $validate->check($input, [
                        'name' => [
                            'required' => true,
                            'min' => 2,
                            'max' => 50
                        ]
                    ]);
                    
                    if ($validation->passed()) {
                        $user->update(['name' => $input['name']]);
                        
                        $userData = $user->data();
                        $response = [
                            'success' => true,
                            'message' => 'Profile updated successfully',
                            'data' => [
                                'id' => $userData->id,
                                'username' => $userData->username,
                                'name' => $userData->name,
                                'created_at' => $userData->createdAt
                            ]
                        ];
                    } else {
                        $response['message'] = 'Validation failed';
                        $response['errors'] = $validation->errors();
                    }
                } else {
                    $response['message'] = 'Not logged in';
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        case 'change-password':
            if ($request_method === 'POST') {
                $user = new User();
                if ($user->isLoggedIn()) {
                    $input = json_decode(file_get_contents('php://input'), true);
                    
                    if (!$input) {
                        throw new Exception('Invalid JSON data');
                    }
                    
                    $validate = new Validate();
                    $validation = $validate->check($input, [
                        'current_password' => ['required' => true],
                        'new_password' => [
                            'required' => true,
                            'min' => 6
                        ],
                        'confirm_password' => [
                            'required' => true,
                            'matches' => 'new_password'
                        ]
                    ]);
                    
                    if ($validation->passed()) {
                        $userData = $user->data();
                        if ($userData->password === Hash::make($input['current_password'], $userData->salt)) {
                            $salt = Hash::salt(32);
                            $user->update([
                                'password' => Hash::make($input['new_password'], $salt),
                                'salt' => $salt
                            ]);
                            
                            $response = [
                                'success' => true,
                                'message' => 'Password changed successfully'
                            ];
                        } else {
                            $response['message'] = 'Current password is incorrect';
                        }
                    } else {
                        $response['message'] = 'Validation failed';
                        $response['errors'] = $validation->errors();
                    }
                } else {
                    $response['message'] = 'Not logged in';
                }
            } else {
                throw new Exception('Method not allowed');
            }
            break;
            
        default:
            throw new Exception('Endpoint not found');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?> 