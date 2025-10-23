<?php

namespace Demoshop\Local\Infrastructure\routers;

use Closure;

/**
 * Route class that contains HTTP method, path/url, and the target function
 */
class Route
{
    /**
     * @var string Http method
     */
    public string $method;
    /**
     * @var string url for method
     */
    public string $url;
    /**
     * @var String that leads to the route function, ex: 'ProductController::getAllProducts'
     */
    public string $target;

    /**
     * @param string $method
     * @param string $url
     * @param String $target
     */
    public function __construct(string $method, string $url, string $target)
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->target = $target;
    }
}