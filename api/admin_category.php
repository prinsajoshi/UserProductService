<?php
// Include required files and initialize necessary objects
require_once '../config/database.php';
require_once '../middleware/AuthMiddleware.php';
require_once '../controllers/AdminProductController.php';
require_once '../controllers/AdminController.php';

header("Content-Type: application/json; charset=UTF-8");

// Main execution
list($db, $authMiddleware) = initialize();
handleAuthentication($authMiddleware);

$productController = new ProductController($db);
$adminController = new AdminController($db);
$request_method = $_SERVER["REQUEST_METHOD"];
list($adminToken, $data) = getRequestData();

switch ($request_method) {
    case 'POST':
        handlePostRequest($productController, $data);
        break;

    case 'GET':
        handleGetRequest($productController, $adminController, $adminToken, $data);
        break;

    case 'PUT':
        handlePutRequest($productController, $data);
        break;

    case 'DELETE':
        handleDeleteRequest($productController, $adminController, $adminToken, $data);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

// Function to initialize database connection and middleware
function initialize()
{
    $database = new Database();
    $db = $database->getConnection();

    $authMiddleware = new AuthMiddleware($db);
    return [$db, $authMiddleware];
}

// Function to handle authentication
function handleAuthentication($authMiddleware)
{
    $authResponse = $authMiddleware->checkAdminToken();
    if (!$authResponse['status']) {
        echo json_encode(["message" => $authResponse['message']]);
        exit;
    }
}

// Function to get request data
function getRequestData()
{
    $headers = apache_request_headers();
    $adminToken = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    $data = json_decode(file_get_contents("php://input"), true);
    return [$adminToken, $data];
}

// Function to handle POST requests
function handlePostRequest($productController, $data)
{
    if (!empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price']) && isset($data['user_id'])) {
        $response = $productController->createProduct($data['product_name'], $data['description'], $data['category'], $data['price'], $data['user_id']);
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Function to handle GET requests
function handleGetRequest($productController, $adminController, $adminToken, $data)
{
    if (isset($data['category']) && !isset($data['product_id'])) {
        $category = $data['category'] ?? null;
        $response = $adminController->getCategory($adminToken, $category);
        echo json_encode($response);
    } elseif (isset($data['category'])) {
        $category = $data['category'];
        $products = $productController->getProductsByCategory($category);
        echo json_encode($products);
    } elseif (!isset($data['category']) && isset($data['product_id'])) {
        $products = $productController->getAllProducts();
        echo json_encode($products);
    } else {
        $products = $productController->getAllProducts();
        echo json_encode($products);
    }
}

// Function to handle PUT requests
function handlePutRequest($productController, $data)
{
    if (!empty($data['product_id']) && !empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price']) && isset($data['user_id'])) {
        $response = $productController->updateProduct($data['product_id'], $data['product_name'], $data['description'], $data['category'], $data['price'], $data['user_id']);
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Function to handle DELETE requests
function handleDeleteRequest($productController, $adminController, $adminToken, $data)
{
    if (isset($data['category']) && !isset($data['product_id'])) {
        $category = $data['category'];
        $response = $adminController->deleteCategory($adminToken, $category);
        echo json_encode([
            "message" => $response ? "Category deleted successfully" : "Failed to delete category"
        ]);
    } elseif (isset($data['product_id'])) {
        $response = $productController->deleteProduct($data['product_id']);
        echo json_encode($response);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}
?>
