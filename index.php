<?php

require_once __DIR__ . '/vendor/autoload.php';

use Demoshop\Local\Infrastructure\Bootstrap;
use Demoshop\Local\Infrastructure\routers\Router;
use Demoshop\Local\Infrastructure\http\HttpRequest;

/**
 * Entry point for the Demo Shop application.
 *
 * initializes bootstrap and uses Router to direct HTTP traffic
 */

$bootstrap = new Bootstrap();
$serviceRegistry = $bootstrap->init();

$router = new Router();

// Load routes
$routes = require_once __DIR__ . '/src/Presentation/routes.php';
$router->initialize($routes, 'Demoshop\\Local\\Presentation\\controllers');

// Run the app (router takes care of everything)
$router->dispatch($serviceRegistry->get(HttpRequest::class), $serviceRegistry);
