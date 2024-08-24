<?php
require_once '../helper_api/helper_login.php';

header("Content-Type: application/json");

$data = getRequestData();
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    // Validate the request and terminate if an error occurs
    $validatedData = Middleware::validateRequest($data);

    // Initialize the appropriate controller based on the role
    $controller = initialize($validatedData['role']);

    // Handle authentication based on the role
    handleAuthentication($controller, $validatedData);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Invalid request method"
    ]);
}
?>
