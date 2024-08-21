<?php
require_once '../models/Admin.php';
require_once '../models/User.php';
require_once '../models/UserProduct.php'; // Assuming you have a Product model

class AdminController {
    private $adminModel;
    private $userModel;
    private $productModel;

    public function __construct($db) {
        $this->adminModel = new Admin($db);  // Admin model object
        $this->userModel = new User($db);
        $this->productModel = new UserProduct($db);
    }

    // Authenticate admin and return token if successful
    public function authenticate($username, $password) {
        $this->adminModel->username = $username;
        $this->adminModel->password = $password;

        if ($this->adminModel->checkCredentials()) {
            $this->adminModel->updateToken(); // Update the token in the database
            return [
                "message" => "Correct password",
                "token" => $this->adminModel->token
            ];
        } else {
            return [
                "message" => "Incorrect password"
            ];
        }
    }

    // Create a new user if admin token is valid
    public function createUser($username, $password, $adminToken) {
        if ($this->adminModel->isTokenValid($adminToken)) {
            $this->userModel->username = $username;
            $this->userModel->password = $password; // No need to encode as base64 for user password
            return $this->userModel->createUser();
        } else {
            return [
                "message" => "Invalid admin token"
            ];
        }
    }

    // Delete a user if admin token is valid
    public function deleteUser($userId, $adminToken) {
        if ($this->adminModel->isTokenValid($adminToken)) {
            return $this->userModel->deleteUser($userId);
        } else {
            return [
                "message" => "Invalid admin token"
            ];
        }
    }

    // Get products by ID or all products if ID is not provided
    public function getProducts($adminToken, $productId = null) {
        if ($this->adminModel->isTokenValid($adminToken)) {
            return $this->productModel->getProducts($productId);
        } else {
            return [
                "message" => "Invalid admin token"
            ];
        }
    }

    // Get products by category if admin token is valid
    public function getCategory($adminToken, $category = null) {
        if ($this->adminModel->isTokenValid($adminToken)) {
            return $this->productModel->getCategory($category);
        } else {
            return [
                "message" => "Invalid admin token"
            ];
        }
    }

    // Delete a category if admin token is valid
    public function deleteCategory($adminToken, $category = null) {
        if ($this->adminModel->isTokenValid($adminToken)) {
            return $this->productModel->deleteCategory($category);
        } else {
            return [
                "message" => "Invalid admin token"
            ];
        }
    }
}
?>
