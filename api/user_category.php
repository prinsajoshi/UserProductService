<?php
require_once '../config/database.php';
require_once '../controllers/UserProductController.php';
require_once '../controllers/UserController.php';

header("Content-Type: application/json; charset=UTF-8");

// Initialize database connection and controllers
function initialize()
{
    $database = new Database();
    $db = $database->getConnection();
    $productController = new ProductController($db);
    $userController = new UserController($db);
    return [$productController, $userController];
}

// Get request headers
function getRequestHeaders()
{
    return apache_request_headers();
}

// Get request data
function getRequestData()
{
    return json_decode(file_get_contents("php://input"), true);
}

// Fetch products based on category or all products
function fetchProducts($productController, $user, $data)
{
    $category = $data['category'] ?? null;

    if ($category) {
        $products = $productController->getProductsByCategory($category, $user['user_id']);
        echo json_encode($products ?: ["message" => "No products found in this category"]);
    } else {
        $products = $productController->getAllProducts($user['user_id']);
        echo json_encode($products);
    }
}

// Main execution
list($productController, $userController) = initialize();
$headers = getRequestHeaders();
$token = $headers['Authorization'] ?? '';
$user = $userController->getUserByToken($token);

if ($user) {
    $data = getRequestData();
    fetchProducts($productController, $user, $data);
} else {
    echo json_encode(["message" => "Invalid or missing token"]);
}
?>
