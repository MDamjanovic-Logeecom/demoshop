<?php

namespace Demoshop\Local\Infrastructure\routers;

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\HttpResponse;
use Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Class for redirecting according to HTTP method and URL in route
 *
 * contains an array of all routes loaded from routes.php
 */
class Router
{
    /** @var Route[] that contains all routes loaded in routes.php */
    protected array $routes = [];

    public function initialize(array $routes): void
    {
        foreach ($routes as $route) {
            $this->routes[] = $route;
        }
    }

    public function dispatch(HttpRequest $request, ServiceRegistry $registry): void
    {
        try {
            $this->handleRequest($request, $registry);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    protected function handleRequest(HttpRequest $request, ServiceRegistry $registry): void
    {
        $method = $request->getServer('REQUEST_METHOD');
        $url = parse_url($request->getServer('REQUEST_URI'), PHP_URL_PATH);
        $url = str_replace('/index.php', '', $url) ?: '/';

        foreach ($this->routes as $route) {
            if ($route->method !== $method) {
                continue;
            }

            $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $route->url);

            if (!preg_match("#^$pattern$#", $url, $matches)) {
                continue;
            }

            array_shift($matches);

            // Collect parameter names from the route URL
            preg_match_all('#\{([\w]+)\}#', $route->url, $paramNames);

            $routeParams = array_combine($paramNames[1], $matches);
            $request->setRouteParams($routeParams);

            // Call controller method
            [$controllerName, $methodName] = explode('::', $route->target);
            $controllerClass = "Demoshop\\Local\\Presentation\\controllers\\$controllerName";
            $controller = $registry->get($controllerClass);

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Controller method not found: $controllerName::$methodName");
            }

            $result = call_user_func_array(
                [$controller, $methodName],
                $matches ? [$request, ...$matches] : [$request]
            );

            if ($result instanceof HttpResponse) {
                $result->send();
            }

            return;
        }

        throw new Exception("Route not found for $method $url", 404);
    }

    protected function handleException(Exception $exception): void
    {
        $code = $exception->getCode() ?: 500;
        http_response_code($code);
        echo "<h1>Error $code</h1><p>" . htmlspecialchars($exception->getMessage()) . "</p>";
    }

    /**
     * Match the current request to a route and execute it
     */
//    public function matchRoute(HttpRequest $request): void
//    {
//        $method = $request->getServer('REQUEST_METHOD');
//        $url = parse_url($request->getServer('REQUEST_URI'), PHP_URL_PATH);
//
//        // removes /index.php prefix if present
//        $url = str_replace('/index.php', '', $url);
//        if ($url === '') {
//            $url = '/';
//        }
//
//        foreach ($this->routes as $route) {
//            if ($route->method !== $method) {
//                continue;
//            }
//
//            // Converting {param} to regex
//            $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $route->url);
//
//            if (preg_match("#^$pattern$#", $url, $matches)) {
//                array_shift($matches);
//                call_user_func_array($route->target, $matches);
//                return;
//            }
//        }
//
//        throw new Exception("Route not found for $method $url");
//    }
}
