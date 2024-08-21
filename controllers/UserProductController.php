<?php
require_once '../models/UserProduct.php';

class ProductController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new UserProduct($db);
    }

    //creates the product
    public function createProduct($product_name, $description, $category, $price, $user_id) {
        $this->productModel->product_name = $product_name;
        $this->productModel->description = $description;
        $this->productModel->category = $category;
        $this->productModel->price = $price;
        $this->productModel->user_id = $user_id;

        if ($this->productModel->createProduct()) {
            return "Product created successfully";
        } else {
            return "Failed to create product";
        }
    }

    //returns all the product infromation
    public function getAllProducts($user_id) {
        return $this->productModel->getAllProducts($user_id);
    }

    //returns all the product by id
    public function getProductById($product_id, $user_id) {
        return $this->productModel->getProductById($product_id, $user_id);
    }

    //update the product by id
    public function updateProduct($product_id, $product_name, $description, $category, $price, $user_id) {
        return $this->productModel->updateProduct($product_id, $product_name, $description, $category, $price, $user_id);
    }

    //delete the product by id
    public function deleteProduct($product_id, $user_id) {
        return $this->productModel->deleteProduct($product_id, $user_id);
    }

    //get the products by categories
    public function getProductsByCategory($category, $user_id=null) {
        return $this->productModel->getProductsByCategory($category, $user_id);
    }


    
}
