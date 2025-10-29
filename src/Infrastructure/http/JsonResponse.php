<?php

namespace Demoshop\Local\Infrastructure\http;

class JsonResponse extends Response
{
    private array $data;

    public function __construct(array $data, int $statusCode = 200)
    {
        parent::__construct($statusCode);
        $this->data = $data;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo json_encode($this->data);
    }
}