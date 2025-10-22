<?php

namespace Demoshop\Local\Infrastructure\routers;

use Closure;

/**
 * Route class that contains HTTP method, path/url, and the required runnable closure
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
     * @var Closure that does the redirecting and logic
     */
    public Closure $target;

    /**
     * @param string $method
     * @param string $url
     * @param Closure $target
     */
    public function __construct(string $method, string $url, Closure $target)
    {
        $this->method = strtoupper($method);
        $this->url = $url;
        $this->target = $target;
    }
}