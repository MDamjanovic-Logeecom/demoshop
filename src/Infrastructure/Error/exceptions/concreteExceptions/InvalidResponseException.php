<?php

namespace Demoshop\Local\Infrastructure\Error\exceptions\concreteExceptions;

use Demoshop\Local\Infrastructure\Error\exceptions\IException;

class InvalidResponseException extends \Exception implements IException
{
    public function __construct(
    string $message = "Invalid Response.",
    int $code = 502,
    ?\Throwable $previous = null
) {
    parent::__construct($message, $code, $previous);
}
}