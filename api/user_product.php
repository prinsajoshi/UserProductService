<?php
require_once '../config/database.php';
require_once '../controllers/UserProductController.php';
require_once '../controllers/UserController.php';

/* Initialize the database connection and controllers.
 */
function initialize()
{
    $database = new Database();
    $db = $database->getConnection();
    $productController = new ProductController($db);
    $userController = new UserController($db);
    return [$productController, $userController];
}

/* Get the request headers.
 */
function getRequestHeaders()
{
    return apache_request_headers();
}

/* Get the request data from the input stream.
 */
function getRequestData()
{
    return json_decode(file_get_contents("php://input"), true);
}

/*Handle GET request for products.
 */
function handleGetRequest($productController, $user, $data)
{
    if (isset($data['product_id'])) {
        $productId = intval($data['product_id']);
        $product = $productController->getProductById($productId, $user['user_id']);
        echo json_encode($product ?: ["message" => "Product not found"]);
    } else {
        $products = $productController->getAllProducts($user['user_id']);
        echo json_encode($products);
    }
}

/*Handle POST request to create a product.
 */
function handlePostRequest($productController, $user, $data)
{
    if (!empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price'])) {
        $response = $productController->createProduct(
            $data['product_name'],
            $data['description'],
            $data['category'],
            $data['price'],
            $user['user_id']
        );
        echo json_encode(["message" => $response]);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

/* Handle PUT request to update a product.
 */
function handlePutRequest($productController, $user, $data)
{
    if (!empty($data['product_id']) && !empty($data['product_name']) && !empty($data['description']) && !empty($data['category']) && isset($data['price'])) {
        $response = $productController->updateProduct(
            intval($data['product_id']),
            $data['product_name'],
            $data['description'],
            $data['category'],
            floatval($data['price']),
            $user['user_id']
        );
        echo json_encode(["message" => $response ? "Product updated successfully" : "Failed to update product"]);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

/* Handle DELETE request to delete a product.
 */
function handleDeleteRequest($productController, $user, $data)
{
    if (!empty($data['product_id'])) {
        $response = $productController->deleteProduct(intval($data['product_id']), $user['user_id']);
        echo json_encode(["message" => $response ? "Product deleted successfully" : "Failed to delete product"]);
    } else {
        echo json_encode(["message" => "Product ID required"]);
    }
}

// Main execution
header("Content-Type: application/json; charset=UTF-8");

list($productController, $userController) = initialize();
$headers = getRequestHeaders();
$token = $headers['Authorization'] ?? '';
$user = $userController->getUserByToken($token);
$data = getRequestData();
$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($user) {
    switch ($requestMethod) {
        case 'GET':
            handleGetRequest($productController, $user, $data);
            break;
        case 'POST':
            handlePostRequest($productController, $user, $data);
            break;
        case 'PUT':
            handlePutRequest($productController, $user, $data);
            break;
        case 'DELETE':
            handleDeleteRequest($productController, $user, $data);
            break;
        default:
            echo json_encode(["message" => "Invalid request method"]);
            break;
    }
} else {
    echo json_encode(["message" => "Invalid or missing token"]);
}

?>