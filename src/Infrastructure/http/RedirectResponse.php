<?php

namespace Demoshop\Local\Infrastructure\http;

/**
 * HttpResponse for redirecting to another route.
 */
class RedirectResponse extends Response
{
    /**
     * @var string URL to the wanted route for redirection
     */
    private string $url;

    public function __construct(string $url, int $statusCode = 302)
    {
        parent::__construct($statusCode);
        $this->url = $url;
    }

    /**
     * Sends response back to client. Redirects to wanted route.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header("Location: {$this->url}");
        exit;
    }
}
