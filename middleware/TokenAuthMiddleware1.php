<?php
require_once '../models/Admin.php';
require_once '../models/User.php';

class AuthMiddleware {
    private $adminModel;
    private $userModel;

    public function __construct($db) {
        $this->adminModel = new Admin($db);
        $this->userModel = new User($db);
    }

    public function checkToken($token) {
        // Ensure the token is provided
        if (empty($token)) {
            return [
                "status" => false,
                "message" => "Token not provided"
            ];
        }
        elseif ($this->adminModel->isTokenValid($token)) {
            return [
                "status" => true,
                "role" => "admin"
            ];
        }
        elseif ($this->userModel->isTokenValid($token)) {
            return [
                "status" => true,
                "role" => "user"
            ];
        }
        else{
            return [
                "status" => false,
                "message" => "Invalid Token"
            ];
    }
    }
}
?>
