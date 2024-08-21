<?php
class User {
    private $conn;
    private $table_name = "users";

    public $username;
    public $user_id;
    public $password;
    public $role = 'user';  // Default role
    public $token = '';     // Blank token

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser() {
        $this->password = base64_encode($this->password);


        $query = "INSERT INTO " . $this->table_name . " (username, password, role, token) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $this->username, $this->password, $this->role, $this->token);

        if ($stmt->execute()) {
            return [
                "message" => "User created successfully"
            ];
        } else {
            return [
                "message" => "Error creating user"
            ];
        }
    }

    public function generateToken() {
        // Generate a random token
        $this->token = bin2hex(random_bytes(16));
        $query = "UPDATE " . $this->table_name . " SET token=? WHERE username=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("ss", $this->token, $this->username);

        if ($stmt->execute()) {
            return $this->token;
        }

        return false;
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

    public function deleteUser($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getUserByToken() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE token = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $this->token);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return false;
    }

}
?>
