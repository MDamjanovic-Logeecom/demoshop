<?php

// get the page name from the URL
$page = $_GET['page'] ?? 'list'; // default page is the list

switch ($page) {
    case 'add':
        require 'presentation/views/add_product.php';
        break;

    case 'edit':
        $sku = $_GET['sku'] ?? null;
        require 'presentation/views/edit_product.php';
        break;

    case 'list':
    default:
        require 'presentation/views/products_list.php';
        break;
}

