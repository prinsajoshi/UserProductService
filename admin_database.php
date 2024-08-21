<?php
require_once '../project_php/config/database.php';

class Admin {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function insertAdmin($username, $password, $role, $token = "") {
        $stmt = $this->db->prepare("INSERT INTO admin (username, password, role, token) VALUES (?, ?, ?, ?)");
        
        if ($stmt === false) {
            die("Error preparing statement: " . $this->db->error);
        }
        
        $hashedPassword = base64_encode($password);

        $stmt->bind_param("ssss", $username, $hashedPassword, $role, $token);
        
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
        
        $stmt->close();
    }
}

// Usage
$database = new Database();
$admin = new Admin($database->getConnection());

// Insert new admin with username "admin", password "admin", role "admin", and blank token
$admin->insertAdmin("admin", "admin", "admin");
?>
