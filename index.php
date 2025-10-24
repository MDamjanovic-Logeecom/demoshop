<?php

require_once __DIR__ . '/vendor/autoload.php';

use Demoshop\Local\Bootstrap\Bootstrap;
use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\routers\Router;

/**
 * Entry point for the Demo Shop application.
 *
 * initializes bootstrap and uses Router to direct HTTP traffic
 */

$registry = new ServiceRegistry();
$bootstrap = new Bootstrap($registry);
$bootstrap->init();

$router = new Router();

// Load routes
$routes = require_once __DIR__ . '/src/Bootstrap/routes.php';
$router->initialize($routes, 'Demoshop\\Local\\Presentation\\controllers');

$router->dispatch($registry->get(HttpRequest::class), $registry);
