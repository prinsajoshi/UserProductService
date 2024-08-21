<?php
require_once '../models/User.php';
require_once '../models/Admin.php';

class UserController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function authenticate($username, $password) {
        $this->userModel->username = $username;
        $this->userModel->password = $password;

        if ($this->userModel->checkCredentials()) {
            $token = $this->userModel->generateToken();
            if ($token) {
                return [
                    "message" => "Correct password",
                    "token" => $token
                ];
            }
        }

        return ["message" => "Incorrect password"];
    }

    public function getUserByToken($token) {
        $this->userModel->token = $token;
        return $this->userModel->getUserByToken();
    }
}
?>
