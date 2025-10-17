<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\IProductService;
use Demoshop\Local\Data\ProductRepository;
use Demoshop\Local\Infrastructure\http\HttpRequestClass;
use Demoshop\Local\Infrastructure\http\HttpResponseClass;

/**
 * Class ProductController
 *
 * Handles HTTP requests related to products.
 * Uses ProductService for business logic and returns HttpResponseClass objects.
 */
class ProductController
{
    /**
     * @var IProductService Service layer for product-related operations.
     * Concrete instance is injected in the constructor.
     */
    private IProductService $service;

    /**
     * ProductController constructor.
     *
     * Initializes the ProductService with its repository.
     */
    public function __construct(IProductService $service)
    {
        $this->service = $service;
    }

    //------------------------------------------------------------------------------------ Data (service) calls:

    /**
     * Retrieve all products and prepare HTTP response with products_list view.
     *
     * @param HttpRequestClass $request HTTP request object
     * @return HttpResponseClass Response containing status, headers, and view with data
     */
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

    /**
     * Deletes a product based on the SKU received via POST request.
     *
     * @param HttpRequestClass $request The HTTP request object containing POST data
     * @return HttpResponseClass HTTP response indicating the result of the deletion.
     *                             - 302 Redirect on success/failure with status message in URL
     *                             - 405 Method Not Allowed if request is not POST or delete_sku not provided
     */
    public function deleteProductBySKU(HttpRequestClass $request): HttpResponseClass
    {
        $response = new HttpResponseClass();

        if (!$request->isPost() || $request->getPost('delete_sku') === null) {
            $response->setStatusCode(405); // If request method is not POST (S.C. method not allowed)

            return $response;
        }

        // If request is POST
        $sku = $request->getPost('delete_sku');
        $deleted = $this->service->deleteBySKU($sku); // Asking service to delete

        $status = $deleted ? 'success' : 'error';
        $message = $deleted ? 'Product deleted successfully' : 'Failed to delete product';

        // Redirect using HttpResponseClass with message in URL (will be cleared in message.js)
        $response->setStatusCode(302);
        $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));

        return $response;
    }

    /**
     * Updates an existing product with the submitted POST data and optional uploaded image.
     *
     * @param HttpRequestClass $request The HTTP request object containing POST data and files
     * @return HttpResponseClass HTTP response indicating the result of the edit.
     *                             - 302 Redirect after POST with status message in URL
     */
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

    /**
     * Creates a new product using submitted POST data and optional uploaded image.
     *
     * @param HttpRequestClass $request The HTTP request object containing POST data and uploaded files
     * @return HttpResponseClass HTTP response object for redirection after creation
     *                             - 302 Redirect with status message in URL
     */
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

    /**
     * Prepares the response for the display of the edit product form.
     *
     * Fetches the product by SKU. Does not render the form view directly.
     * Instead, the product data and view filename are stored in the response body.
     *
     * @param string|null $sku The SKU of the product to edit; nullable for safety
     * @return HttpResponseClass The HTTP response object containing:
     *                            - 200 OK status and view data if product exists
     *                            - 400 Bad Request status if SKU is missing
     *                            - 404 Not Found if product is not found
     */
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