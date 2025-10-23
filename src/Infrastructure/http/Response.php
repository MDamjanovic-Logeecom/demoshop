<?php

namespace Demoshop\Local\Infrastructure\http;

/**
 * Abstract HttpResponse
 */
abstract class Response
{
    /**
     * @var int status code.
     */
    protected int $statusCode;

    public function __construct(int $statusCode = 200)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Sends the response to the client
     */
    abstract public function send(): void;

    /**
     * @return int status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}