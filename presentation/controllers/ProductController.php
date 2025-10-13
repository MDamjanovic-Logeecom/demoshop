<?php

//namespace controllers;
require_once __DIR__ . '/../../business/ProductService.php';
require_once __DIR__ . '/../../db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductController {
    private ProductService $service;

    public function __construct(PDO $pdo) {
        $repository = new ProductRepository($pdo);
        $this->service = new ProductService($repository);
    }

    public function getAllProducts(): array {
        return $this->service->getAll();
    }

    public function deleteProductBySKU(string $sku): bool {
        //TODO:
        return false;
    }

    //TODO other crud operations:
}