<?php
$input = json_decode(file_get_contents("php://input"), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

$user = new User();
if ($user->login($username, $password)) {
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user" => [
            "id" => $user->data()->id,
            "username" => $user->data()->username,
            "role_id" => $user->data()->role_id,
            "email" => $user->data()->email,
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
}
