<?php
require_once '../config/database.php';
require_once '../controllers/AdminController.php';

header("Content-Type: application/json");

// Initialize database connection and controller
function initialize()
{
    $database = new Database();
    $dbConnection = $database->getConnection();
    $controller = new AdminController($dbConnection);
    return $controller;
}

// Get request data
function getRequestData()
{
    return json_decode(file_get_contents("php://input"));
}

// Handle authentication
function handleAuthentication($controller, $data)
{
    if (isset($data->username) && isset($data->password)) {
        $response = $controller->authenticate($data->username, $data->password);
        echo json_encode($response);
    } else {
        echo json_encode([
            'message' => 'Username and password required'
        ]);
    }
}

// Main execution
$controller = initialize();
$data = getRequestData();
handleAuthentication($controller, $data);
?>
