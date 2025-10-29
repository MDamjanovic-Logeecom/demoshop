<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Service\IProductService;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use http\Exception\InvalidArgumentException;

class FragmentController
{
    /**
     * @var IProductService Service layer for product-related operations.
     * Concrete instance is injected in the constructor.
     */
    private IProductService $service;

    /**
     * ProductController constructor.
     *
     * Initializes the ProductService with its repository.
     */
    public function __construct(IProductService $service)
    {
        $this->service = $service;
    }

    public function layout(): HtmlResponse
    {
        return new HtmlResponse('admin/admin_layout.php');
    }

    public function dashboard(): HtmlResponse
    {
        return new HtmlResponse('admin/dashboard.php');
    }

    public function categories(): HtmlResponse
    {
        return new HtmlResponse('admin/categories.php');
    }

    public function products(): HtmlResponse
    {
        $products = $this->service->getAll();
        return new HtmlResponse('admin/products_list.php', ['products' => $products]);
    }
}