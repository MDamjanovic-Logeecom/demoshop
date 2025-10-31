<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Service\ICategoryService;
use Demoshop\Local\Business\Interfaces\Service\IProductService;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\JsonResponse;

/**
 * Controller for reaching for fragments to display in the single page application part of the application
 */
class FragmentController
{
    /**
     * @var IProductService Service layer for product-related operations.
     * Concrete instance is injected in the constructor.
     */
    private IProductService $productService;
    private ICategoryService $categoryService;

    /**
     * ProductController constructor.
     *
     * Initializes the ProductService with its repository.
     */
    public function __construct(IProductService $productService, ICategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
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
     * @return JsonResponse
     */
    public function dashboard(HttpRequest $request): JsonResponse
    {
        $products = $this->productService->getAll();
        $productCount = count($products);

        $categories = $this->categoryService->getAll();
        $categoriesCount = count($categories);

        // dummy values for now
        $homePageViews = 0;
        $mostViewedProduct = 'N/A';
        $productViews = 0;

        return new JsonResponse([
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
     * @return JsonResponse
     */
    public function categories(HttpRequest $request): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return new JsonResponse($categories);
    }

    /**
     * Sends the products list fragment to be displayed in the layout shell.
     *
     * @return JsonResponse
     */
    public function products(): JsonResponse
    {
        $products = $this->productService->getAll();

        $data = array_map(fn($product) => [
            'sku' => $product->sku,
            'title' => $product->title,
            'brand' => $product->brand,
            'category' => $product->category,
            'shortDescription' => $product->shortDescription,
            'description' => $product->description,
            'enabled' => $product->enabled,
            'price' => $product->price,
            'image' => $product->image,
        ], $products);

        return new JsonResponse($data);
    }
}