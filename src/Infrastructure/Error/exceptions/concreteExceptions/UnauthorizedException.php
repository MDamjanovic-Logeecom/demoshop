<?php

namespace Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions;

use Demoshop\Local\Infrastructure\Error\exceptions\IException;

/**
 * Exception thrown when user is not authorized to access resource
 */
class UnauthorizedException extends \Exception implements IException
{
    public function __construct(
        string $message = "Unauthorized access",
        int $code = 403,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
