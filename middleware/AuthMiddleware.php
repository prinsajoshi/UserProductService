<?php
require_once '../models/Admin.php';

class AuthMiddleware {
    private $adminModel;

    public function __construct($db) {
        $this->adminModel = new Admin($db);
    }

    public function checkAdminToken() {
        $headers = apache_request_headers();
        $token = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if (empty($token)) {
            return [
                "status" => false,
                "message" => "Token is missing"
            ];
        }

        $this->adminModel->token = $token;
        if ($this->adminModel->isTokenValid($token)) {
            return [
                "status" => true
            ];
        } else {
            return [
                "status" => false,
                "message" => "Invalid token"
            ];
        }
    }
}
?>
