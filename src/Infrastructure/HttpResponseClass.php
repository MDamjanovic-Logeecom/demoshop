<?php

class HttpResponseClass
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $body;

    public function setStatusCode(int $code): void
    {
        $this->statusCode = $code;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function setBody(mixed $content): void
    {
        $this->body = $content;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    // For sending the http response to client (status, headers, body...)
    public function send(): void
    {
        // Built in PHP func. that sets the S.C. that the browser will see
        http_response_code($this->statusCode);

        // Send headers (in associative array ex: 'Content-Type' => 'text/html'...)
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Send body
        if ($this->body !== null) {
            if (is_array($this->body) && isset($this->body['view'])) { // Is it a structured resp. containing a view
                $data = $this->body;
                extract($data); // Makes $products (or other variables) available in the view
                require __DIR__ . '/../Presentation/views/' . $data['view'];
            } else {
                echo $this->body; // For plain string responses
            }
        }
    }
}