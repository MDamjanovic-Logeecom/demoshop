<?php

use Demoshop\Local\Business\IProductService;
use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\routers\Router;
use Demoshop\Local\Presentation\controllers\ProductController;

/**
 * Returns Method-URL-Target closure for each route
 *
 * @param Router $router
 *
 * @param ServiceRegistry $serviceRegistry
 *
 * @return void
 */
return function (Router $router, ServiceRegistry $serviceRegistry) {
    $productService = $serviceRegistry->get(IProductService::class);
    $request = $serviceRegistry->get(HttpRequest::class);

    /**
     * Retrieves the admin product list and displays it.
     */
    $router->addRoute('GET', '/admin/products', function () use ($request, $productService) {
        $controller = new ProductController($productService);
        $response = $controller->getAllProducts($request);
        $response->send();
        exit;
    });

    /**
     * Displays form for creating new product.
     */
    $router->addRoute('GET', '/admin/products/create', function () use ($request, $productService) {
        require __DIR__ . '/../../Presentation/views/add_product.php';
        exit;
    });

    /**
     * Sends form data to server containing new product data.
     */
    $router->addRoute('POST', '/admin/products/create', function () use ($request, $productService) {
        $controller = new ProductController($productService);
        $response = $controller->addProduct($request);
        $response->send();
        exit;
    });

    /**
     * Sends request to delete a product.
     */
    $router->addRoute('POST', '/admin/products/delete', function () use ($request, $productService) {
        $controller = new ProductController($productService);
        $response = $controller->deleteProductBySKU($request);

        $response->send();
        exit;
    });

    /**
     * Displays form for editing products containing current product data.
     */
    $router->addRoute('GET', '/admin/products/{sku}', function ($sku) use ($request, $productService) {
        $controller = new ProductController($productService);
        $response = $controller->showEditForm($sku);

        if ($response->getStatusCode() !== 200) {
            echo $response->getBody();
            exit;
        }

        $data = $response->getBody();
        $product = $data['product'];
        require __DIR__ . '/../../Presentation/views/' . $data['view'];
        exit;
    });

    /**
     * Sends Request to update the current product open in the edit form.
     */
    $router->addRoute('POST', '/admin/products/{sku}', function ($sku) use ($request, $productService) {
        $controller = new ProductController($productService);
        $response = $controller->editProduct($request);
        $response->send();
        exit;
    });

    /**
     * Default for now - if someone navigates to demoshop.local -> takes them to .../admin/products
     */
    $router->addRoute('GET', '/', function () {
        header('Location: /admin/products');
        exit;
    });
};
