<?php
use Demoshop\Local\Infrastructure\HttpRequestClass;
use Demoshop\Local\Infrastructure\Wrapper;
use Demoshop\Local\Presentation\controllers\ProductController;

/**
 * Entry point for the Demo Shop application.
 *
 * Handles routing based on the 'page' GET parameter.
 *
 * Routes:
 * - add: Shows the add product form (GET) or handles product creation (POST)
 * - edit: Shows the edit form for a product (GET) or processes product edits (POST)
 * - delete: Deletes a product via POST
 * - list (default): Displays all products
 *
 * The script uses:
 * - HttpRequestClass for accessing GET, POST, FILES, and SERVER data
 * - ProductController for all product-related actions
 * - Wrapper for access to superglobals if needed
 *
 * Notes:
 * - GET requests render views directly
 * - POST requests return HttpResponseClass objects, which are sent to the browser
 */

require_once __DIR__ . '/../../../vendor/autoload.php';
$request = new HttpRequestClass();
$wrapper = new Wrapper();

// get the page name from the URL
$page = $wrapper->getGet('page') ?? 'list'; // default page is the list

switch ($page) {
    case 'add':
        $controller = new ProductController();

        if (!$request->isPost()) { // GET request -> show add form w/o data
            require '../views/add_product.php';
            break;
        }

        // Form submitted -> POST creation
        $response = $controller->addProduct($request);
        $response->send(); // send headers + body to browser
        break;

    case 'edit':
        $controller = new ProductController();

        // 2 options: load product info to fill table / post an edit
        if ($request->isPost()) {
            // POST: Form submitted -> process edit
            $response = $controller->editProduct($request);
            $response->send();
            break;
        }
        // GET: show edit form
        $sku = $request->getGet('sku') ?? null;
        $response = $controller->showEditForm($sku); // Asking service to fetch product by sku

        if ($response->getStatusCode() !== 200) {
            echo $response->getBody();
            break;
        }

        $data = $response->getBody();
        $product = $data['product'];
        require __DIR__ . '/../views/' . $data['view']; // Sends to the view from response
        break;

    case 'delete':
        $controller = new ProductController();
        $response = $controller->deleteProductBySKU($request);
        $response->send();
        break;

    case 'list':
    default:
        $controller = new ProductController();
        $response = $controller->getAllProducts($request);
        $response->send(); // This will include the view and pass $products
        break;

}

