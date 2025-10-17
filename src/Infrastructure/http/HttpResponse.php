<?php
namespace Demoshop\Local\Infrastructure\http;

/**
 * Class representing an HTTP response.
 */
class HttpResponse
{
    /** @var int HTTP status code (default 200) */
    private int $statusCode = 200;

    /** @var array Associative array of headers ('Header-Name' => 'value') */
    private array $headers = [];

    /** @var mixed Response body, can be string or structured data for views */
    private mixed $body;

    /**
     * Sets the HTTP status code.
     *
     * @param int $code The HTTP status code (e.g., 200, 404, 302).
     */
    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    /**
     * Sets a single HTTP header.
     *
     * @param string $name Name of the header (e.g., 'Location', 'Content-Type').
     * @param string $value Value of the header.
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Sets the response body.
     *
     * @param mixed $content The content to send; can be string, array with view info, etc.
     */
    public function setBody(mixed $content): void
    {
        $this->body = $content;
    }

    /**
     * Gets the current HTTP status code.
     *
     * @return int HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets all HTTP headers set for the response.
     *
     * @return array Associative array of headers ('Header-Name' => 'value').
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Gets the current response body.
     *
     * @return mixed The body content, can be a string or structured data for views.
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    /**
     * Sends the HTTP response to the client.
     *
     * This includes:
     *   - Setting the HTTP status code using http_response_code()
     *   - Sending all headers
     *   - Sending the response body
     *       - If body is an array containing a 'view' key, extracts variables and requires the view file
     *       - Otherwise, echoes the body directly
     */
    public function send(): void
    {
        // Built in PHP func. that sets the S.C. that the browser will see
        http_response_code($this->statusCode);

        // Send headers (in associative array ex: 'Content-Type' => 'text/html'...)
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Send body
        if (is_array($this->body) && isset($this->body['view'])) { // Is it a structured resp. containing a view
            $data = $this->body;
            extract($data, EXTR_SKIP); // Makes $products (or other variables) available in the view
            require __DIR__ . '/../../Presentation/views/' . $data['view'];

            return;
        }
        // Send plain body
        echo $this->body;
    }
}