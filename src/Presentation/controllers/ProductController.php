<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Service\IProductService;
use Demoshop\Local\DTO\ProductDTO;
use Demoshop\Local\Infrastructure\http\ErrorResponse;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\RedirectResponse;

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
     * @return HtmlResponse Response containing status, headers, and view with data
     */
    public function getAllProducts(HttpRequest $request): HtmlResponse
    {
        $products = $this->service->getAll();
        $response = new HtmlResponse('products_list.php', ['products' => $products], 200);

        return $response;
    }

    /**
     * Deletes a product based on the SKU received via POST request.
     *
     * @param HttpRequest $request The HTTP request object containing POST data
     *
     * @return RedirectResponse|ErrorResponse HTTP response indicating the result of the deletion.
     */
    public function deleteProductBySKU(HttpRequest $request): RedirectResponse|ErrorResponse
    {
        if (!$request->isPost() || $request->getHttpPost('delete_sku') === null) {
            return new ErrorResponse('Invalid request method or missing SKU.', 405); // 405 = Method Not Allowed
        }

        // If request is POST
        $sku = $request->getHttpPost('delete_sku');
        $deleted = $this->service->deleteBySKU($sku); // Asking service to delete

        $status = $deleted ? 'success' : 'error';
        $message = $deleted ? 'Product deleted successfully' : 'Failed to delete product';

        $redirectUrl = "/admin/products?status={$status}&message=" . urlencode($message);

        return new RedirectResponse($redirectUrl);
    }

    /**
     * Updates an existing product with the submitted POST data and optional uploaded image.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and files
     *
     * @return RedirectResponse HTTP response indicating the result of the edit.
     */
    public function editProduct(HttpRequest $request): RedirectResponse
    {
        $productDTO = $this->collectFormData($request);

        $returnDTO = $this->service->update($productDTO);

        $success = true;
        if ($returnDTO == null) {
            $success = false;
        }

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product edited successfully.' : 'Failed to edit product.'; // For displaying alerts

        $redirectUrl = "/admin/products?status={$status}&message=" . urlencode($message);

        return new RedirectResponse($redirectUrl);
    }

    /**
     * Creates a new product using submitted POST data and optional uploaded image.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and uploaded files.
     *
     * @return RedirectResponse HTTP response object for redirection after creation.
     */
    public function addProduct(HttpRequest $request): RedirectResponse
    {
        $productDTO = $this->collectFormData($request);

        $returnDTO = $this->service->create($productDTO);

        $success = true;
        if ($returnDTO == null) {
            $success = false;
        }

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product added successfully.' : 'Failed to add product.';

        $redirectUrl = "/admin/products?status={$status}&message=" . urlencode($message);

        return new RedirectResponse($redirectUrl);
    }

    /**
     * Prepares the response for the display of the edit product form.
     *
     * Fetches the product by SKU. Does not render the form view directly.
     * Instead, the product data and view filename are stored in the response body.
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse|ErrorResponse The HTmlResponse.
     */
    public function showEditForm(HttpRequest $request): HtmlResponse|ErrorResponse
    {
        $sku = $request->getHttpGet('sku') ?? $request->getRouteParam('sku') ?? null;

        if (!$sku) {
            return new ErrorResponse('No SKU provided.', 400); // 400 Bad Request
        }

        $productDTO = $this->service->getBySKU($sku);

        if (!$productDTO) {
            return new ErrorResponse('Product not found.', 404); // 404 Not Found
        }

        return new HtmlResponse('edit_product.php', ['product' => $productDTO], 200);
    }

    /**
     * Returns the create product form
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse (HtmlResponse)
     */
    public function showAddForm(HttpRequest $request): HtmlResponse
    {
        return new HtmlResponse('add_product.php');
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
