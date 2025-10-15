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
    //TODO Retrieves all products
//    public function getAllProducts(): array
//    {
//
//        return $this->service->getAll();
//    }
    public function getAllProducts(HttpRequestClass $request): HttpResponseClass
    {
        $products = $this->service->getAll();

        $response = new HttpResponseClass();
        $response->setStatusCode(200);
        $response->setBody([
            'view' => 'products_list.php',
            'products' => $products
        ]);

        return $response;
    }

    //Deletes product
    public function deleteProductBySKU(HttpRequestClass $request): HttpResponseClass
    {
        $response = new HttpResponseClass();

        if ($request->isPost() && $request->getPost('delete_sku') !== null) {
            $sku = $request->getPost('delete_sku');
            $deleted = $this->service->deleteBySKU($sku); // Asking service to delete

            $status = $deleted ? 'success' : 'error';
            $message = $deleted ? 'Product deleted successfully' : 'Failed to delete product';

            // Redirect using HttpResponseClass with message in URL (will be cleared in message.js)
            $response->setStatusCode(302);
            $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));
        } else {
            // If request method is not POST
            $response->setStatusCode(405);
        }

        return $response;
    }

    // Edits products
    public function editProduct(HttpRequestClass $request): HttpResponseClass
    {
        $imageFile = $request->getFiles('image') ?? null;

        $success = $this->service->update($request->getPostArray(), $imageFile); // Asks service to update product

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product edited successfully.' : 'Failed to edit product.'; // For displaying alerts

        $response = new HttpResponseClass();
        $response->setStatusCode(302); // redirect after POST
        $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));

        return $response;
    }


    // Creates new product
    public function addProduct(HttpRequestClass $request): HttpResponseClass
    {
        $imageFile = $request->getFiles('image') ?? null;
        $success = $this->service->create($request->getPostArray(), $imageFile); // Asks service to add new product

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product added successfully.' : 'Failed to add product.'; // For displaying alerts

        $response = new HttpResponseClass();
        $response->setStatusCode(302); // 302 = redirect
        $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));

        return $response;
    }

    //Retrieves product by sku, redirects to the edit form for set product
    public function showEditForm(?string $sku): HttpResponseClass
    {
        $response = new HttpResponseClass();

        if (!$sku) {
            $response->setStatusCode(400);
            $response->setBody('No SKU provided.');
            return $response;
        }

        $product = $this->service->getBySKU($sku);

        if (!$product) {
            $response->setStatusCode(404);
            $response->setBody('Product not found.');
            return $response;
        }

        // Instead of rendering directly, the product is stored in response content
        // which can be later used by the index file
        $response->setStatusCode(200);
        $response->setBody(['view' => 'edit_product.php', 'product' => $product]);

        return $response;
    }
}