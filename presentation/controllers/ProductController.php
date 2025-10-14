<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

    //------------------------------------------------------------------------------------ Data (service) calls:

    public function getAllProducts(): array {
        return $this->service->getAll();
    }

    public function deleteProductBySKU(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_sku'])) { // Received sky through hidden parameter
            $sku = $_POST['delete_sku'];
            $deleted = $this->service->deleteBySKU($sku);

            if ($deleted) {
                echo "<script>alert('Product deleted successfully.');</script>";
            } else {
                echo "<script>alert('Product not found or could not be deleted.');</script>";
            }

            header("Location: /index.php?page=list");
            exit;
        }
    }

    public function editProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imageFile = $_FILES['image'] ?? null;

            // Pass the file to service — let service decide if it’s valid
            $success = $this->service->update($_POST, $imageFile);

            if ($success) {
                echo "<script>alert('Product edited successfully.');window.location.href='index.php?page=list';</script>";
            } else {
                echo "<script>alert('Error saving product.');window.location.href='index.php?page=list';</script>";
            }
        }
    }

    public function addProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imageFile = $_FILES['image'] ?? null;

            // Pass the file to service — let service decide if it’s valid
            $success = $this->service->create($_POST, $imageFile);

            if ($success) {
                echo "<script>alert('Product created successfully.');window.location.href='index.php?page=list';</script>";
            } else {
                echo "<script>alert('Error creating product.');window.location.href='index.php?page=list';</script>";
            }
        }
    }
    //TODO other crud operations:


    //------------------------------------------------------------------------------------- Other operations:

    public function showEditForm(?string $sku): void {
        if (!$sku) {
            die("No SKU provided.");
        }

        $product = $this->service->getBySKU($sku);
        if (!$product) { // sanity check
            die("Product not found.");
        }

        // Pass $product to the view (this page officially part of this class' scope and can reach any variable here)
        require 'presentation/views/edit_product.php';
    }

}