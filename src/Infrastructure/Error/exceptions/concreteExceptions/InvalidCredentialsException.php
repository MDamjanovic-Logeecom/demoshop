<?php

namespace Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions;

use Demoshop\Local\Infrastructure\Error\exceptions\IException;

class InvalidCredentialsException extends \Exception implements IException
{
    public function __construct(
        string $message = "Invalid username or password",
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}