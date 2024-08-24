<?php
require_once '../helper_api/helper_category.php';

header("Content-Type: application/json; charset=UTF-8");

// Main execution
list($db, $authMiddleware) = initialize();
$headers = getRequestHeaders();
$authResponse = handleAuthentication($authMiddleware, $headers['Authorization']);
$requestMethod = $_SERVER["REQUEST_METHOD"];
$data = getRequestData();

// Create appropriate object based on the role
if ($authResponse['role'] === 'admin') {
    $productController = new AdminProductController($db);
    $adminController = new AdminController($db);
} else {
    $productController = new UserProductController($db);
    $userController = new UserController($db);
}

// Check status before any operation
if ($authResponse['status']) {
    switch ($requestMethod) {
        case 'POST':
            handlePostRequest($productController, $data);
            break;

        case 'GET':
            handleGetRequest($productController, $adminController ?? $userController, $data, $authResponse['role'], $headers['Authorization']);
            break;

        case 'PUT':
            handlePutRequest($productController, $data, $authResponse['role']);
            break;

        case 'DELETE':
            handleDeleteRequest($productController, $adminController ?? null, $data, $authResponse['role']);
            break;

        default:
            echo json_encode([
                'status' => false,
                'message' => 'Invalid request method'
            ]);
            break;
    }
} else {
    echo json_encode([
        'status' => false,
        'message' => $authResponse["message"]
    ]);
}
?>
