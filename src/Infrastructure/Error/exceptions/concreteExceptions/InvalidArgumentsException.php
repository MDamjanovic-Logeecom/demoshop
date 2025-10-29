<?php

namespace Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions;

use Demoshop\Local\Infrastructure\Error\exceptions\IException;

class InvalidArgumentsException extends \Exception implements IException
{
    public function __construct(string $message = "Invalid arguments provided", int $code = 400)
    {
        parent::__construct($message, $code);
    }
}