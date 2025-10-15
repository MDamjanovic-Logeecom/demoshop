<?php

class Wrapper
{
    private array $server;
    private array $get;
    private array $post;
    private array $files;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
    }

    public function getServer(string $key, mixed $default = null): mixed
    {   // Handles if key value not present -> return the default value presented
        return $this->server[$key] ?? $default;
    }

    public function getGet(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function getPostObj(): array
    {
        return $this->post;
    }

    public function getFiles(string $key, mixed $default = null): mixed
    {
        return $this->files[$key] ?? $default;
    }

}