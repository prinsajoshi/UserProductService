<?php
require_once '../config/database.php';
require_once '../models/EnumProductCategory.php';

class Product {
    private $conn;
    private $table_name = "products";

    public $product_id;
    public $product_name;
    public $description;
    public $category;
    public $price;
    public $user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createProduct() {
        if (!ProductCategory::isValidCategory($this->category)) {
            return false; // Invalid category
        }
        $query = "INSERT INTO " . $this->table_name . " (product_name, description, category, price, user_id) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdi", $this->product_name, $this->description, $this->category, $this->price, $this->user_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAllProducts() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getProductById($product_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateProduct() {
        if (!ProductCategory::isValidCategory($this->category)) {
            return false; // Invalid category
        }
        $query = "UPDATE " . $this->table_name . " SET product_name = ?, description = ?, category = ?, price = ? WHERE product_id = ? AND user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssdii", $this->product_name, $this->description, $this->category, $this->price, $this->product_id, $this->user_id);

        return $stmt->execute();
    }

    public function deleteProduct($product_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);

        return $stmt->execute();
    }

    public function getProductsByCategory($category) {
        if (!ProductCategory::isValidCategory($category)) {
            return []; // Invalid category
        }
        $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();

        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
}
?>
