<?php

namespace Demoshop\Local\Infrastructure\http;

use Demoshop\Local\Presentation\View;

/**
 * HTTPResponse that is used to send back a view
 */
class  HtmlResponse extends Response
{
    /**
     * @var string path to the returned view in the project
     */
    private string $viewName;

    /**
     * @var array
     */
    private array $data;

    public function __construct(string $view, array $data = [], int $statusCode = 200)
    {
        parent::__construct($statusCode);
        $this->viewName = $view;
        $this->data = $data;
    }

    /**
     * Sends the HTTP response to the client.
     * has the view ready for use.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        $view = new View($this->viewName, $this->data);
        $view->navigate();
    }
}
