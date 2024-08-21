<?php
require_once '../config/database.php';
require_once '../models/EnumProductCategory.php';

class UserProduct {
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

    //insert the data of product in the database
    public function createProduct() {
        if (!ProductCategory::isValidCategory($this->category)) {
            return false; // Invalid category
        }
        $query = "INSERT INTO " . $this->table_name . " SET product_name=?, description=?, category=?, price=?, user_id=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("sssdi", $this->product_name, $this->description,$this->category, $this->price, $this->user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    //returns the product on the basis of user_id
    public function getAllProducts($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //returns the specific product based on the id
    public function getProductById($product_id, $user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("ii", $product_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    //update the product based on user_id and product_id 
    public function updateProduct($product_id, $product_name, $description,$category, $price, $user_id) {
        if (!ProductCategory::isValidCategory($category)) {
            return false; // Invalid category
        }
        $query = "UPDATE " . $this->table_name . " SET product_name=?, description=?, category=?, price=? WHERE product_id=? AND user_id=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("sssdii", $product_name, $description, $category, $price, $product_id, $user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    //deletes the product based on the product_id and user_id 
    public function deleteProduct($product_id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE product_id=? AND user_id=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("ii", $product_id, $user_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getProducts($productId = null) {
        if ($productId) {
            // Get a specific product by ID
            $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $productId);
        } else {
            // Get all products
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    public function getCategory($category=null) {
        if ($category) {
            // Get a products of specific category
            $query = "SELECT * FROM " . $this->table_name . " WHERE category = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $category);
        } else {
            // Get all products
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    public function deleteCategory($category=null) {
        $query = "DELETE FROM " . $this->table_name . " WHERE category=?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $category);

        if ($stmt->execute()) {
            return true;
        }


        return false;
    }

    public function getProductsByCategory($category, $userId = null) {
        if (!ProductCategory::isValidCategory($category)) {
            return []; // Invalid category
        }
        if ($userId) {
            $query = "SELECT * FROM products WHERE category = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $category, $userId);
        } else {
            $query = "SELECT * FROM products WHERE category = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $category);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    
}
