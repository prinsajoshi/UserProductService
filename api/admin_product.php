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
    return $adminController;
}

// Get request headers
function getRequestHeaders()
{
    return apache_request_headers();
}

// Get request data
function getRequestData()
{
    return json_decode(file_get_contents("php://input"));
}

// Handle GET request for products
function handleGetRequest($adminController, $adminToken, $data)
{
    $productId = isset($data->product_id) ? intval($data->product_id) : null;
    $response = $adminController->getProducts($adminToken, $productId);
    echo json_encode($response);
}

// Main execution
$adminController = initialize();
$headers = getRequestHeaders();
$adminToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';
$data = getRequestData();
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'GET') {
    handleGetRequest($adminController, $adminToken, $data);
} else {
    echo json_encode([
        'message' => 'Invalid request method'
    ]);
}

?>

