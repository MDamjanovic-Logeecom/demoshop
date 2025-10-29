<?php

namespace Demoshop\Local\Presentation\middleware\handlers;

use Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions\UnauthorizedException;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Presentation\helper\SessionManager;
use Demoshop\Local\Presentation\middleware\Middleware;

/**
 * Middleware that checks if user is authorized to access route.
 * If authorized, delegates to the next handler in chain if not null.
 *
 * Part of the Chain Of Responsibility design pattern.
 */
class AuthorizationMiddleware implements Middleware
{
    /**
     * Next middleware in chain
     *
     * @var Middleware|null
     */
    private Middleware|null $nextMiddleware = null;
    /**
     * @var HttpRequest|null
     */
    private HttpRequest|null $currentRequest = null;

    /**
     * Checking validity of authorization in the given request.
     *
     * @param HttpRequest $request
     *
     * @return bool
     *
     * @throws UnauthorizedException
     */
    public function middlewareCheck(HttpRequest $request): bool
    {
        $this->currentRequest = $request;

        $sessionManager = SessionManager::getInstance();
        if (!$sessionManager->isLoggedIn()) {
            throw new UnauthorizedException();
        }

        if ($this->nextMiddleware) {
            return $this->nextCheck();
        }

        return true;
    }

    /**
     * Delegates the request to the next middleware in the chain if not null.
     *
     * @return bool
     */
    private function nextCheck(): bool
    {
        return $this->nextMiddleware->middlewareCheck($this->currentRequest);
    }

    /**
     * Setter for the next middleware in the chain of responsibility
     */
    public function setNextMiddleware(?Middleware $nextMiddleware): void
    {
        $this->nextMiddleware = $nextMiddleware;
    }
}
