<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductController
{
    private ProductService $service;

    public function __construct()
    {
        $repository = new ProductRepository();
        $this->service = new ProductService($repository);
    }

    //------------------------------------------------------------------------------------ Data (service) calls:

    public function getAllProducts(): array
    {

        return $this->service->getAll();
    }

    public function deleteProductBySKU(): void
    {
        $wrapper = new Wrapper();

        if ($wrapper->getServer('REQUEST_METHOD') === 'POST' && $wrapper->getPost('delete_sku') !== null) { // Received sku through hidden parameter
            $sku = $wrapper->getPost('delete_sku');
            $deleted = $this->service->deleteBySKU($sku);

            $status = $deleted ? 'success' : 'error';
            $message = $deleted ? 'Product deleted successfully' : 'Failed to delete product';

            // A redirect with message in URL (will be cleared in message.js)
            header("Location: /index.php?page=list&status={$status}&message=" . urlencode($message));
            exit;
        }
    }

    public function editProduct(): void
    {
        $wrapper = new Wrapper();

        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            $imageFile = $wrapper->getFiles('image') ?? null;

            $success = $this->service->update($wrapper->getPostObj(), $imageFile);

            $status = $success ? 'success' : 'error';
            $message = $success ? 'Product edited successfully.' : 'Failed to edit product.';

            header("Location: /index.php?page=list&status={$status}&message=" . urlencode($message));
        }
    }

    public function addProduct(): void
    {
        $wrapper = new Wrapper();

        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            $imageFile = $wrapper->getFiles('image') ?? null;

            $success = $this->service->create($wrapper->getPostObj(), $imageFile);

            $status = $success ? 'success' : 'error';
            $message = $success ? 'Product added successfully.' : 'Failed to add product.';

            header("Location: /index.php?page=list&status={$status}&message=" . urlencode($message));
        }
    }


    //------------------------------------------------------------------------------------- Other operations:

    public function showEditForm(?string $sku): void
    {
        if (!$sku) {
            die("No SKU provided.");
        }

        $product = $this->service->getBySKU($sku);
        if (!$product) { // sanity check
            die("Product not found.");
        }

        // Pass $product to the view (this page officially part of this class' scope and can reach any variable here)
        require __DIR__ . '/../views/edit_product.php';
    }

}