<?php

class HttpRequestClass
{
    private array $post;
    private array $get;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    // Retrieves POST field with default value
    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    // Retrieves all POST data
    public function getPostArray(): array
    {
        return $this->post;
    }

    // Retrieve GET field
    public function getGet(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    // Retrieves a file or all files
    public function getFiles(string $key = null): mixed
    {
        if ($key === null) {
            return $this->files;
        }
        return $this->files[$key] ?? null;
    }

    // Retrieves SERVER field
    public function getServer(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    // Shortcut to check request method from the server
    public function isPost(): bool
    {
        return strtoupper($this->getServer('REQUEST_METHOD', 'GET')) === 'POST';
    }
}