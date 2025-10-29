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
     * @param string $controllerDirectoryPath
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
        $this->handleRequest($request, $registry);
    }

    /**
     * Handles HttpRequests, parses routes, and delegates to target functions.
     * Sends
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
        $method = strtoupper($request->getServer('REQUEST_METHOD'));
        $url = parse_url($request->getServer('REQUEST_URI'), PHP_URL_PATH);
        $url = str_replace('/index.php', '', $url) ?: '/';

        foreach ($this->routes as $routeItem) {
            $route = $this->extractMatchingRoute($routeItem, $request, $url);
            if (!$route instanceof Route) {
                continue;
            }

            [$controllerClass, $methodName] = $this->resolveController($route->target);
            $controller = $registry->get($controllerClass);

            if (!method_exists($controller, $methodName)) {
                throw new Exception("Controller method not found: {$controllerClass}::{$methodName}");
            }

            $params = $this->extractRouteParams($route, $url);
            $request->setRouteParams($params);

            $response = call_user_func([$controller, $methodName], $request, ...array_values($params));

            if (!$response instanceof Response) {
                throw new Exception("Controller must return a Response object");
            }

            $response->send();
            return;
        }

        throw new Exception("Route not found for {$method} {$url}", 404);
    }

    /**
     * If this route group matches the URL prefix and route from HttpRequest passes
     * the middleware checks, returns the route in question.
     */
    private function extractMatchingRoute(RouteGroup $routeGroup, HttpRequest $request, string $url): ?Route
    {
        if (!$routeGroup->matchesPrefix($url)) {
            return null;
        }

        $routeGroup->middlewareCheck($request);

        return $routeGroup->findMatchingRoute($request);
    }

    /**
     * Extract route parameters from the URL.
     */
    private function extractRouteParams(Route $route, string $url): array
    {
        preg_match_all('#\{([\w]+)\}#', $route->url, $paramNames);
        $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $route->url);
        preg_match("#^{$pattern}$#", $url, $matches);
        array_shift($matches);

        return !empty($paramNames[1]) ? array_combine($paramNames[1], $matches) : [];
    }

    /**
     * Split controller target into class and method.
     */
    private function resolveController(string $target): array
    {
        [$controllerName, $methodName] = explode('::', $target);
        return ["{$this->controllerDirectoryPath}\\{$controllerName}", $methodName];
    }
}
