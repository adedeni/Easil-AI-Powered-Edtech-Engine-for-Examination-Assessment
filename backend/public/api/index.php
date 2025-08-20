<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Autoload Core
require_once '../../app/Core/init.php';

// Allow CORS (for React frontend)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json");

// If OPTIONS request (preflight), just exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Example: easil.com.ng/api/login → ["api","login"]
$endpoint = isset($requestUri[1]) ? $requestUri[1] : null;

// Route the request
switch ($endpoint) {
    case 'login':
        require_once 'routes/login.php';
        break;

    case 'users':
        require_once 'routes/users.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        break;
}
