<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../autoload.php';
$request = new HttpRequestClass();
$wrapper = new Wrapper();

// get the page name from the URL
$page = $wrapper->getGet('page') ?? 'list'; // default page is the list

switch ($page) {
    case 'add':
        $controller = new ProductController();

        if ($request->isPost()) {
            // Form submitted -> POST creation
            $response = $controller->addProduct($request);
            $response->send(); // send headers + body to browser
        } else {
            // GET request -> show add form w/o data
            require '../views/add_product.php';
        }
        break;

    case 'edit':
        $controller = new ProductController();

        // 2 options: load product info to fill table / post an edit
        if ($request->isPost()) {
            // POST: Form submitted -> process edit
            $response = $controller->editProduct($request);
            $response->send();
        } else {
            // GET: show edit form
            $sku = $request->getGet('sku') ?? null;
            $response = $controller->showEditForm($sku); // Asking service to fetch product by sku

            if ($response->getStatusCode() === 200) {
                $data = $response->getBody();
                $product = $data['product'];
                require __DIR__ . '/../views/' . $data['view']; // Sends to the view from response
            } else {
                echo $response->getBody();
            }
        }
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

