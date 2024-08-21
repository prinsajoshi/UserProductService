<?php
require_once '../config/database.php';
require_once '../controllers/AdminController.php';

header("Content-Type: application/json");

// Initialize database connection and controller
function initialize()
{
    $database = new Database();
    $dbConnection = $database->getConnection();
    $adminController = new AdminController($dbConnection);
    return [$dbConnection, $adminController];
}

// Get request data and headers
function getRequestData()
{
    $data = json_decode(file_get_contents("php://input"));
    $headers = apache_request_headers();
    $adminToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    return [$data, $adminToken];
}

// Handle POST request
function handlePostRequest($adminController, $data, $adminToken)
{
    if (isset($data->username) && isset($data->password) && $adminToken) {
        $response = $adminController->createUser($data->username, $data->password, $adminToken);
        echo json_encode($response);
    } else {
        echo json_encode([
            'message' => 'Username, password, and admin token required'
        ]);
    }
}

// Handle DELETE request
function handleDeleteRequest($adminController, $data, $adminToken)
{
    if (isset($data->user_id) && $adminToken) {
        $response = $adminController->deleteUser(intval($data->user_id), $adminToken);
        echo json_encode([
            "message" => $response ? "User deleted successfully" : "Failed to delete user"
        ]);
    } else {
        echo json_encode([
            'message' => 'User ID and admin token required'
        ]);
    }
}

// Main execution
list($dbConnection, $adminController) = initialize();
$requestMethod = $_SERVER["REQUEST_METHOD"];
list($data, $adminToken) = getRequestData();

switch ($requestMethod) {
    case 'POST':
        handlePostRequest($adminController, $data, $adminToken);
        break;

    case 'DELETE':
        handleDeleteRequest($adminController, $data, $adminToken);
        break;

    default:
        echo json_encode([
            'message' => 'Invalid request method'
        ]);
        break;
}

?>


