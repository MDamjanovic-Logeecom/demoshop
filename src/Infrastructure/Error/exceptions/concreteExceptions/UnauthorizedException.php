<?php

namespace Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions;

use Demoshop\Local\Infrastructure\Error\exceptions\IException;

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