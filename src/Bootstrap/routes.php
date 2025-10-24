<?php

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\routers\Route;
use Demoshop\Local\Infrastructure\routers\Router;

/**
 * Returns Method-URL-Target closure for each route
 *
 * @param Router $router
 *
 * @param ServiceRegistry $serviceRegistry
 *
 * @return void
 */
return [
    new Route('GET',  '/admin/products',          'ProductController::getAllProducts'),
    new Route('GET',  '/admin/products/create',   'ProductController::showAddForm'),
    new Route('POST', '/admin/products/create',   'ProductController::addProduct'),
    new Route('POST', '/admin/products/delete',   'ProductController::deleteProductBySKU'),
    new Route('GET',  '/admin/products/{sku}',    'ProductController::showEditForm'),
    new Route('POST', '/admin/products/{sku}',    'ProductController::editProduct'),
    new Route('GET',  '/',                        'ProductController::getAllProducts'), //for now, redirects to product list page
    new Route('POST', '/register',                'UserController::register'),
    new Route('GET',  '/admin',                   'UserController::showLoginPage'),
    new Route('POST', '/admin',                   'UserController::login'),
];
