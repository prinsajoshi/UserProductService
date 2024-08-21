<?php
require_once '../config/database.php';
require_once '../controllers/UserController.php';

/* Initialize the database connection and UserController*/
function initialize()
{
    $database = new Database();
    $db = $database->getConnection();
    return new UserController($db);
}

/* Get request data from the input stream*/
function getRequestData()
{
    return json_decode(file_get_contents("php://input"));
}

/*Handle POST request for user authentication*/
function handlePostRequest($userController, $data)
{
    if (!empty($data->username) && !empty($data->password)) {
        $response = $userController->authenticate($data->username, $data->password);
        echo json_encode(["message" => $response]);
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

/* Main execution*/
header("Content-Type: application/json; charset=UTF-8");

$userController = initialize();
$requestMethod = $_SERVER["REQUEST_METHOD"];
$data = getRequestData();

switch ($requestMethod) {
    case 'POST':
        handlePostRequest($userController, $data);
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

?>
