<?php

use Demoshop\Local\Infrastructure\Bootstrap;
use Demoshop\Local\Infrastructure\routers\Router;
use Demoshop\Local\Infrastructure\http\HttpRequest;

/**
 * Entry point for the Demo Shop application.
 *
 * initializes bootstrap and uses Router to direct HTTP traffic
 */

require_once __DIR__ . '/vendor/autoload.php';

$bootstrap = new Bootstrap();
$serviceRegistry = $bootstrap->init();

$router = new Router();

// Load and register all routes
$loadRoutes = require_once __DIR__ . '/src/Infrastructure/routers/routes.php';
$loadRoutes($router, $serviceRegistry);

// Matching current request
try {
    $router->matchRoute($serviceRegistry->get(HttpRequest::class));
} catch (Exception $exception) {
    http_response_code(404);
    echo $exception->getMessage();
}
