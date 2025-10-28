<?php

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\routers\Route;
use Demoshop\Local\Infrastructure\routers\RouteGroup;
use Demoshop\Local\Infrastructure\routers\Router;
use Demoshop\Local\Presentation\middleware\handlers\AuthorizationMiddleware;

/**
 * Returns Method-URL-Target closure for each route
 *
 * @param Router $router
 *
 * @param ServiceRegistry $serviceRegistry
 *
 * @return void
 */

$adminRoutes = [
    new Route('GET',  '/admin/products',          'ProductController::getAllProducts'),
    new Route('GET',  '/admin/products/create',   'ProductController::showAddForm'),
    new Route('POST', '/admin/products/create',   'ProductController::addProduct'),
    new Route('POST', '/admin/products/delete',   'ProductController::deleteProductBySKU'),
    new Route('GET',  '/admin/products/{sku}',    'ProductController::showEditForm'),
    new Route('POST', '/admin/products/{sku}',    'ProductController::editProduct'),
];

$publicRoutes = [
    new Route('GET',  '/',                        'UserController::showLoginPage'),
    new Route('POST', '/register',                'UserController::register'),
    new Route('GET',  '/loginPage',               'UserController::showLoginPage'),
    new Route('POST', '/login',                   'UserController::login'),
];

$adminGroup = new RouteGroup($adminRoutes, '/admin');
$adminGroup->addMiddleware(new AuthorizationMiddleware());

$publicGroup = new RouteGroup($publicRoutes, '/');

return [
    'admin' => $adminGroup,
    'public' => $publicGroup,
];
