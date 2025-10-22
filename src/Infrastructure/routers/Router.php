<?php

namespace Demoshop\Local\Infrastructure\routers;

use Closure;
use Demoshop\Local\Infrastructure\http\HttpRequest;
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

    /**
     * Register a new route HTTP method, url/path, target closure.
     */
    public function addRoute(string $method, string $url, Closure $target): void
    {
        $this->routes[] = new Route($method, $url, $target);
    }

    /**
     * Match the current request to a route and execute it
     */
    public function matchRoute(HttpRequest $request): void
    {
        $method = $request->getServer('REQUEST_METHOD');
        $url = parse_url($request->getServer('REQUEST_URI'), PHP_URL_PATH);

        // removes /index.php prefix if present
        $url = str_replace('/index.php', '', $url);
        if ($url === '') {
            $url = '/';
        }

        foreach ($this->routes as $route) {
            if ($route->method !== $method) {
                continue;
            }

            // Converting {param} to regex
            $pattern = preg_replace('#\{[\w]+\}#', '([\w\-]+)', $route->url);

            if (preg_match("#^$pattern$#", $url, $matches)) {
                array_shift($matches);
                call_user_func_array($route->target, $matches);
                return;
            }
        }

        throw new Exception("Route not found for $method $url");
    }
}
