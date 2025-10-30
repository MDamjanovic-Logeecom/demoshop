<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Service\IProductService;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use Demoshop\Local\Infrastructure\http\HttpRequest;

/**
 * Controller for reaching for fragments to display in the single page application part of the application
 */
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

    /**
     * Displays the shell of the SPA part of the application.
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse
     */
    public function layout(HttpRequest $request): HtmlResponse
    {
        return new HtmlResponse('admin/admin_layout.php');
    }

    /**
     * Sends the admin dashboard fragment to be displayed in the layout shell.
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse
     */
    public function dashboard(HttpRequest $request): HtmlResponse
    {
        $products = $this->service->getAll();
        $productCount = count($products);

        // dummy values for now
        $categoriesCount = 0;
        $homePageViews = 0;
        $mostViewedProduct = 'N/A';
        $productViews = 0;

        return new HtmlResponse('admin/dashboard.php', [
            'productCount' => $productCount,
            'categoriesCount' => $categoriesCount,
            'homePageViews' => $homePageViews,
            'mostViewedProduct' => $mostViewedProduct,
            'productViews' => $productViews,
        ]);
    }

    /**
     * Sends the categories fragment to be displayed in the layout shell.
     *
     * @param HttpRequest $request
     *
     * @return HtmlResponse
     */
    public function categories(HttpRequest $request): HtmlResponse
    {
        return new HtmlResponse('admin/categories.php');
    }

    /**
     * Sends the products list fragment to be displayed in the layout shell.
     *
     * @return HtmlResponse
     */
    public function products(): HtmlResponse
    {
        $products = $this->service->getAll();
        return new HtmlResponse('admin/products_list.php', ['products' => $products]);
    }
}