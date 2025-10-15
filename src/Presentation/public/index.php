<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../autoload.php';
$wrapper = new Wrapper();

// get the page name from the URL
$page = $wrapper->getGet('page') ?? 'list'; // default page is the list

switch ($page) {
    case 'add':
        $controller = new ProductController();

        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            // Form submitted -> POST creation
            $controller->addProduct();
        } else {
            // GET request -> show add form w/o data
            require '../views/add_product.php';
        }
        break;

    case 'edit':
        $controller = new ProductController();

        // 2 options: load product info to fill table / post an edit
        if ($wrapper->getServer('REQUEST_METHOD') === 'POST') {
            // Form submitted -> process edit
            $controller->editProduct();
        } else {
            // GET request -> show form
            $sku = $wrapper->getGet('sku') ?? null;
            $controller->showEditForm($sku);
        }
        break;

    case 'list':
    default:
        require '../views/products_list.php';
        break;

    case 'delete':
        $controller = new ProductController();
        $controller->deleteProductBySKU();
        break;
}

