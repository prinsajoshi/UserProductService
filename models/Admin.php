<?php
class Admin {
    private $conn;
    private $table_name = "admin";

    public $username;
    public $password;
    public $token;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkCredentials() {
        // Convert the password to base64
        $encoded_password = base64_encode($this->password);

        $query = "SELECT * FROM " . $this->table_name . " WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->username, $encoded_password);

        $stmt->execute();
        $result = $stmt->get_result();   

        return $result->num_rows > 0;  // returns true if data found
    }

    public function updateToken() {
        $this->token = bin2hex(random_bytes(16)); // Generate a random token
        $query = "UPDATE " . $this->table_name . " SET token = ? WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->token, $this->username);
        $stmt->execute();
        $stmt->close();
    }

    public function isTokenValid($token) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}
?>
