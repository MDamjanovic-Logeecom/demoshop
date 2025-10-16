<?php

/**
 * Class Wrapper
 *
 * A helper class to encapsulate global PHP arrays ($_SERVER, $_GET, $_POST, $_FILES)
 * and provide getter methods with default values. This avoids direct use of globals.
 */
class Wrapper
{
    /** @var array Copy of $_SERVER superglobal */
    private array $server;

    /** @var array Copy of $_GET superglobal */
    private array $get;

    /** @var array Copy of $_POST superglobal */
    private array $post;

    /** @var array Copy of $_FILES superglobal */
    private array $files;

    /**
     * Wrapper constructor.
     *
     * Initializes the internal arrays with the current global values.
     */
    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
    }

    /**
     * Retrieves a value from the $_SERVER array.
     *
     * @param string $key The key to look up
     * @param mixed $default Value to return if key does not exist
     * @return mixed
     */
    public function getServer(string $key, mixed $default = null): mixed
    {   // Handles if key value not present -> return the default value presented
        return $this->server[$key] ?? $default;
    }

    /**
     * Retrieves a value from the $_GET array.
     *
     * @param string $key The key to look up
     * @param mixed $default Value to return if key does not exist
     * @return mixed
     */
    public function getGet(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Retrieves a value from the $_POST array.
     *
     * @param string $key The key to look up
     * @param mixed $default Value to return if key does not exist
     * @return mixed
     */
    public function getPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Retrieves all POST data.
     *
     * @return array
     */
    public function getPostObj(): array
    {
        return $this->post;
    }

    /**
     * Retrieves file(s) from the $_FILES array.
     *
     * @param string $key The file key to look up
     * @param mixed $default Value to return if key does not exist
     * @return mixed
     */
    public function getFiles(string $key, mixed $default = null): mixed
    {
        return $this->files[$key] ?? $default;
    }

}