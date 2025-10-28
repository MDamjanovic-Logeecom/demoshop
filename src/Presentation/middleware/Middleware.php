<?php

namespace Demoshop\Local\Presentation\middleware;

use Demoshop\Local\Infrastructure\http\HttpRequest;

/**
 * Middleware interface that checks a condition in order to
 * delegate to the next middleware in chain in the
 * Chain Of Responsibility design pattern.
 */
interface Middleware
{
    /**
     * Checks validity of the given request for certain condition.
     *
     * @param HttpRequest $request
     *
     * @return mixed
     */
    public function middlewareCheck(HttpRequest $request): bool;

    /**
     * Sets next middleware in chain.
     *
     * @param Middleware|null $nextMiddleware
     *
     * @return void
     */
    public function setNextMiddleware(?Middleware $nextMiddleware): void;
}
