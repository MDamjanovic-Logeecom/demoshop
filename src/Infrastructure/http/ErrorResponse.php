<?php

namespace Demoshop\Local\Infrastructure\http;

/**
 * HttpResponse for returning error messages
 */
class ErrorResponse extends Response
{
    /**
     * @var string Error message
     */
    private string $message;

    public function __construct(string $message, int $statusCode = 500)
    {
        parent::__construct($statusCode);
        $this->message = $message;
    }

    /**
     * Sends error message to client.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        echo "<h1>Error {$this->statusCode}</h1><p>" . htmlspecialchars($this->message) . "</p>";
    }
}
