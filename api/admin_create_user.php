<?php
require_once '../helper_api/helper_admin_create_user.php';

header("Content-Type: application/json");

// Main execution
list($adminController, $authMiddleware) = initialize();
$requestMethod = $_SERVER["REQUEST_METHOD"];
list($data, $adminToken) = getRequestData();

// Middleware - Check token
$authResponse = $authMiddleware->checkToken($adminToken);

if ($authResponse['status']) {
    switch ($requestMethod) {
        case 'POST':
            handlePostRequest($adminController, $data);
            break;

        case 'DELETE':
            handleDeleteRequest($adminController, $data);
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
        'message' => $authResponse['message']
    ]);
}

?>
