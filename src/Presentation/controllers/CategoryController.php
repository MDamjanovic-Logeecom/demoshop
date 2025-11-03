<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Business\Interfaces\Service\ICategoryService;
use Demoshop\Local\Infrastructure\http\HtmlResponse;
use Demoshop\Local\Infrastructure\http\HttpRequest;
use Demoshop\Local\Infrastructure\http\JsonResponse;

class CategoryController
{
    /**
     * @var ICategoryService Service layer for product-related operations.
     * Concrete instance is injected in the constructor.
     */
    private ICategoryService $service;

    /**
     * CategoryController constructor.
     *
     * Initializes the CategoryService with its repository.
     */
    public function __construct(ICategoryService $service)
    {
        $this->service = $service;
    }

    public function getAllCategories(HttpRequest $request): HtmlResponse
    {
        $products = $this->service->getAll();
        $response = new HtmlResponse('categories.php', ['products' => $products], 200);

        return $response;
    }

    /**
     * Updates an existing category.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and files
     *
     * @return JsonResponse HTTP response indicating the result of the edit.
     */
//    public function editProduct(HttpRequest $request): JsonResponse
//    {
//        //TODO: finish this shit
//        return null;
//    }
}
