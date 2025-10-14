<?php

require_once 'db_connect.php';
/** @var PDO $pdo */

// get the page name from the URL
$page = $_GET['page'] ?? 'list'; // default page is the list

switch ($page) {
    case 'add':
        require_once 'presentation/controllers/ProductController.php';
        $controller = new ProductController($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Form submitted -> POST creation
            $controller->addProduct();
        } else {
            // GET request -> show add form w/o data
            require 'presentation/views/add_product.php';
        }
        break;

    case 'edit':
        require_once 'presentation/controllers/ProductController.php';
        $controller = new ProductController($pdo);

        // 2 options: load product info to fill table / post an edit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Form submitted -> process edit
            $controller->editProduct();
        } else {
            // GET request -> show form
            $sku = $_GET['sku'] ?? null;
            $controller->showEditForm($sku);
        }
        break;

    case 'list':
    default:
        require 'presentation/views/products_list.php';
        break;

    case 'delete':
        require_once 'presentation/controllers/ProductController.php';

        $controller = new ProductController($pdo);
        $controller->deleteProductBySKU();
        break;
}

