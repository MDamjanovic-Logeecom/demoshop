<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\IProductService;
use Demoshop\Local\DTO\ProductDTO;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\HttpResponse;

/**
 * Class ProductController
 *
 * Handles HTTP requests related to products.
 * Uses ProductService for business logic and returns HttpResponse objects.
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

    /**
     * Retrieve all products and prepare HTTP response with products_list view.
     *
     * @param HttpRequest $request HTTP request object
     *
     * @return HttpResponse Response containing status, headers, and view with data
     */
    public function getAllProducts(HttpRequest $request): HttpResponse
    {
        $products = $this->service->getAll();

        $response = new HttpResponse();
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
     * @param HttpRequest $request The HTTP request object containing POST data
     *
     * @return HttpResponse HTTP response indicating the result of the deletion.
     */
    public function deleteProductBySKU(HttpRequest $request): HttpResponse
    {
        $response = new HttpResponse();

        if (!$request->isPost() || $request->getHttpPost('delete_sku') === null) {
            $response->setStatusCode(405); // If request method is not POST (S.C. method not allowed)

            return $response;
        }

        // If request is POST
        $sku = $request->getHttpPost('delete_sku');
        $deleted = $this->service->deleteBySKU($sku); // Asking service to delete

        $status = $deleted ? 'success' : 'error';
        $message = $deleted ? 'Product deleted successfully' : 'Failed to delete product';

        // Redirect using HttpResponse with message in URL (will be cleared in message.js)
        $response->setStatusCode(302);
        $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));

        return $response;
    }

    /**
     * Updates an existing product with the submitted POST data and optional uploaded image.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and files
     *
     * @return HttpResponse HTTP response indicating the result of the edit.
     */
    public function editProduct(HttpRequest $request): HttpResponse
    {
        $productDTO = $this->collectFormData($request);

        $success = $this->service->update($productDTO); // Asks service to update product

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product edited successfully.' : 'Failed to edit product.'; // For displaying alerts

        $response = new HttpResponse();
        $response->setStatusCode(302); // redirect after POST
        $response->setHeader('Location', "/index.php?page=list&status={$status}&message=" . urlencode($message));

        return $response;
    }

    /**
     * Creates a new product using submitted POST data and optional uploaded image.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and uploaded files.
     *
     * @return HttpResponse HTTP response object for redirection after creation.
     */
    public function addProduct(HttpRequest $request): HttpResponse
    {
        $productDTO = $this->collectFormData($request);

        $success = $this->service->create($productDTO);

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product added successfully.' : 'Failed to add product.'; // For displaying alerts

        $response = new HttpResponse();
        $response->setStatusCode(302);
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
     *
     * @return HttpResponse The HTTP response.
     */
    public function showEditForm(?string $sku): HttpResponse
    {
        $response = new HttpResponse();

        if (!$sku) {
            $response->setStatusCode(400);
            $response->setBody('No SKU provided.');

            return $response;
        }

        $productDTO = $this->service->getBySKU($sku);

        if (!$productDTO) {
            $response->setStatusCode(404);
            $response->setBody('Product not found.');

            return $response;
        }

        // Instead of rendering directly, the product is stored in response content
        // which can be later used by the index file
        $response->setStatusCode(200);
        $response->setBody(['view' => 'edit_product.php', 'product' => $productDTO]);

        return $response;
    }

    /**
     * Collects data from form to avoid duplicate code for POST functionalities.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and uploaded files.
     *
     * @return ProductDTO containing all form entries.
     */
    private function collectFormData(HttpRequest $request): ProductDTO
    {
        $imageFile = $request->getFiles('image') ?? null;
        $imageData = null;

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
            $imageData = file_get_contents($imageFile['tmp_name']);
        }

        $productDTO = new ProductDTO(
            sku: $request->getHttpPost('sku', ''),
            title: $request->getHttpPost('title', ''),
            brand: $request->getHttpPost('brand', ''),
            category: $request->getHttpPost('category', ''),
            shortDescription: $request->getHttpPost('short_description', ''),
            description: $request->getHttpPost('description', ''),
            enabled: $request->getHttpPost('enabled', 0) ? 1 : 0,
            price: (float)$request->getHttpPost('price', 0.0),
            image: $imageData,
        );

        return $productDTO;
    }
}
