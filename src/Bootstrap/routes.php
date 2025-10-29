<?php

use Demoshop\Local\Infrastructure\routers\Route;
use Demoshop\Local\Infrastructure\routers\RouteGroup;
use Demoshop\Local\Presentation\middleware\handlers\AuthorizationMiddleware;

/**
 * Groups and returns routes (Method-URL-Target) in a RouteGroup object
 * grouped by prefix.
 */

$adminRoutes = [
    new Route('GET',  '/admin',                   'FragmentController::layout'),
    new Route('GET',  '/admin/dashboard',         'FragmentController::dashboard'),
    new Route('GET',  '/admin/categories',        'FragmentController::categories'),
    new Route('GET',  '/admin/products',          'FragmentController::products'),
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
