<?php

namespace Demoshop\Local\Infrastructure\routers;

use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Presentation\middleware\Middleware;

/**
 * Groups routes by common url prefix and applies same
 * chain of responsibility middleware verifications.
 */
class RouteGroup
{
    /**
     * @var array of routes in group.
     */
    private array $routes = [];
    /**
     * Array containing chain of middleware applied to route group
     *
     * @var array
     */
    private array $middleware = [];
    /**
     * Common prefix of route group
     *
     * @var string
     */
    private string $prefix = '';

    public function __construct(array $routes, string $prefix)
    {
        $this->routes = $routes;
        $this->prefix = $prefix;
    }

    /**
     * Starts the middleware chain verification off
     *
     * @param HttpRequest $request
     *
     * @return bool
     */
    public function middlewareCheck(HttpRequest $request): bool
    {
        $route = $this->findMatchingRoute($request);
        if (!$route) {
            return false;
        }

        if (empty($this->middleware)) {
            return true;
        }

        $firstMiddleware = $this->middleware[0];

        return $firstMiddleware->middlewareCheck($request);
    }

    /**
     * checks if the route group contains the given route
     *
     * @param Route $route
     *
     * @return bool
     */
    public function containsRoute(Route $route): bool
    {
        if (in_array($route, $this->routes)) {
            return true;
        }

        return false;
    }

    /**
     * Adds nex middleware to the responsibility chain
     *
     * @param Middleware $middleware
     *
     * @return void
     */
    public function addMiddleware(Middleware $middleware): void
    {
        if (empty($this->middleware)) {
            $this->middleware[] = $middleware;

            return;
        }

        $last = array_pop($this->middleware);
        $last->setNextMiddleware($middleware);

        $this->middleware[] = $middleware;
    }

    /**
     * Finds matching route in route group from httpRequest
     *
     * @param HttpRequest $request
     *
     * @return Route|null
     */
    public function findMatchingRoute(HttpRequest $request): ?Route
    {
        $method = $request->getServer('REQUEST_METHOD');
        $path = parse_url($request->getServer('REQUEST_URI'), PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route->method !== $method) {
                continue;
            }

            // Dynamic segments like /admin/products/{sku}
            $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $route->url);

            if (preg_match("#^$pattern$#", $path)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Prefix comparator
     *
     * @param string $url
     *
     * @return bool
     */
    public function matchesPrefix(string $url): bool
    {
        return str_starts_with($url, $this->prefix);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
