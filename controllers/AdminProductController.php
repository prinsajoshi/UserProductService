<?php
require_once '../models/AdminProductCategory.php';
require_once '../models/EnumProductCategory.php';

class ProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new Product($db);
    }

    public function createProduct($product_name, $description, $category, $price, $user_id) {
        $this->productModel->product_name = $product_name;
        $this->productModel->description = $description;
        $this->productModel->category = $category;
        $this->productModel->price = $price;
        $this->productModel->user_id = $user_id;

        if ($this->productModel->createProduct()) {
            return ["message" => "Product created successfully"];
        } else {
            return ["message" => "Failed to create product"];
        }
    }

    public function getAllProducts() {
        return $this->productModel->getAllProducts();
    }

    public function getProductById($product_id) {
        return $this->productModel->getProductById($product_id);
    }

    public function updateProduct($product_id, $product_name, $description, $category, $price, $user_id) {
        $this->productModel->product_id = $product_id;
        $this->productModel->product_name = $product_name;
        $this->productModel->description = $description;
        $this->productModel->category = $category;
        $this->productModel->price = $price;
        $this->productModel->user_id = $user_id;

        if ($this->productModel->updateProduct()) {
            return ["message" => "Product updated successfully"];
        } else {
            return ["message" => "Failed to update product"];
        }
    }

    public function deleteProduct($product_id) {
        if ($this->productModel->deleteProduct($product_id)) {
            return ["message" => "Product deleted successfully"];
        } else {
            return ["message" => "Failed to delete product"];
        }
    }

    public function getProductsByCategory($category) {
        return $this->productModel->getProductsByCategory($category);
    }
}
?>
