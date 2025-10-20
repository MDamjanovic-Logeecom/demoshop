<?php
namespace Demoshop\Local\Infrastructure\http;

/**
 * Class HttpRequest
 *
 * Encapsulates HTTP request data ($_GET, $_POST, $_FILES, $_SERVER)
 * and provides getter methods to safely access them.
 */
class HttpRequest
{
    /** @var array Stores POST data */
    private array $post;

    /** @var array Stores GET data */
    private array $get;

    /** @var array Stores uploaded file data */
    private array $files;

    /** @var array Stores server information */
    private array $server;

    /**
     * HttpRequest constructor.
     *
     * Copies superglobals to internal properties for controlled access.
     */
    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    /**
     * Retrieve a single POST field with an optional default value.
     *
     * @param string $key The POST key to retrieve.
     * @param mixed $default The default value to return if key does not exist.
     *
     * @return mixed The value from POST or the default.
     */
    public function getHttpPost(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Retrieve all POST data as an associative array.
     *
     * @return array All POST fields.
     */
    public function getPostArray(): array
    {
        return $this->post;
    }

    /**
     * Retrieve a single GET field with an optional default value.
     *
     * @param string $key The GET key to retrieve.
     * @param mixed $default The default value if key does not exist.
     *
     * @return mixed The value from GET or the default.
     */
    public function getHttpGet(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Retrieve uploaded file(s).
     *
     * @param string|null $key Optional key of a specific file.
     * @return mixed Array of file(s) if key is null, or a single file array, or null if not found.
     */
    public function getFiles(string $key = null): mixed
    {
        if ($key === null) {
            return $this->files;
        }

        return $this->files[$key] ?? null;
    }

    /**
     * Retrieve a SERVER field with an optional default value.
     *
     * @param string $key The SERVER key to retrieve.
     * @param mixed $default The default value if key does not exist.
     *
     * @return mixed The value from SERVER or the default.
     */
    public function getServer(string $key, mixed $default = null): mixed
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Checks if the current request method is POST.
     *
     * @return bool True if request method is POST, false otherwise.
     */
    public function isPost(): bool
    {
        return strtoupper($this->getServer('REQUEST_METHOD', 'GET')) === 'POST';
    }
}
