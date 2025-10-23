<?php

namespace Demoshop\Local\Presentation;

/**
 * Class for keeping the redirection view file fetching located in the presentation layer
 */
class View
{
    /**
     * @var string path to the wanted view
     */
    private string $viewPath;
    /**
     * @var array data to be loaded into the wanted page
     */
    private array $data;

    /**
     * @param string $viewName name of file to reach ex. formView.php
     * @param array $data to fill the wanted page up with
     */
    public function __construct(string $viewName, array $data = [])
    {
        $this->viewPath = __DIR__ . '/views/' . $viewName;
        $this->data = $data;
    }

    /**
     * Navigates to the wanted page and gives the necessary data to it
     */
    public function navigate(): void
    {
        extract($this->data, EXTR_SKIP);
        require $this->viewPath;
    }
}
