<?php

namespace Demoshop\Local\Infrastructure\routers;

use Demoshop\Local\Infrastructure\DI\ServiceRegistry;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\Response;
use Exception;

/**
 * Class for redirecting according to HTTP method and URL in route
 *
 * contains an array of all routes loaded from routes.php
 */
class Router
{
    /** @var Route[] that contains all routes loaded in routes.php */
    protected array $routes = [];

    private string $controllerDirectoryPath;

    /**
     * Initialize the routes reading them from file.
     *
     * @param array $routes
     *
     * @return void
     */
    public function initialize(array $routes, string $controllerDirectoryPath): void
    {
        $this->controllerDirectoryPath = $controllerDirectoryPath;

        foreach ($routes as $route) {
            $this->routes[] = $route;
        }
    }

    /**
     * Handles incoming requests and catches exceptions.
     *
     * @param HttpRequest $request
     * @param ServiceRegistry $registry
     *
     * @return void
     */
    public function dispatch(HttpRequest $request, ServiceRegistry $registry): void
    {
        try {
            $this->handleRequest($request, $registry);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Handles HttpRequests, parses routes, and delegates to target functions
     *
     * @param HttpRequest $request
     * @param ServiceRegistry $registry
     *
     * @return void
     *
     * @throws Exception
     */
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

            // Parameter names in the route URL
            preg_match_all('#\{([\w]+)\}#', $route->url, $paramNames);

            $routeParams = array_combine($paramNames[1], $matches);
            $request->setRouteParams($routeParams);

            // Call controller method
            [$controllerName, $methodName] = explode('::', $route->target);
            $controllerClass = "$this->controllerDirectoryPath\\$controllerName";
            $controller = $registry->get($controllerClass);

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Controller method not found: $controllerName::$methodName");
            }

            $result = call_user_func_array(
                [$controller, $methodName],
                $matches ? [$request, ...$matches] : [$request]
            );

            if ($result instanceof Response) {
                $result->send();

                return;
            }
            throw new Exception("Controller must return a Response object");
        }
        throw new Exception("Route not found for $method $url", 404);
    }

    /**
     * Handles Exceptions
     *
     * @param Exception $exception
     *
     * @return void
     */
    protected function handleException(Exception $exception): void
    {
        $code = $exception->getCode() ?: 500;
        http_response_code($code);
        echo "<h1>Error $code</h1><p>" . htmlspecialchars($exception->getMessage()) . "</p>";
    }
}
