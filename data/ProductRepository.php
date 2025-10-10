<?php

//namespace data;
//use models\Product;

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../db_connect.php';

class ProductRepository {
    //private PDO $pdo;

    public function __construct() {
        global $pdo;
        //$this->pdo = $pdo;
    }

    public function getAllProducts(): array {
        $products = [];
        //TODO
        return $products;
    }

//    public function getProductBySKU(int $sku): ?Product {
//
//    }

//    public function editProduct(Product $product) {
//
//    }

//    public function addProduct(Product $product) {
//
//    }
}