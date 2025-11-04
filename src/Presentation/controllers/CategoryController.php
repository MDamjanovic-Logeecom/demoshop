<?php

namespace Demoshop\Local\Presentation\controllers;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Business\Interfaces\Service\ICategoryService;
use Demoshop\Local\DTO\CategoryDTO;
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
    public function editCategory(HttpRequest $request): JsonResponse
    {
        // JSON input
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $categoryDTO = $this->collectFormData($data, $request);

        $updatedCategory = $this->service->update($categoryDTO);

        $success = ($updatedCategory !== null);
        $status = $success ? 'success' : 'error';
        $message = $success ? 'Category edited successfully.' : 'Failed to edit category.';

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $updatedCategory ? [
                'id' => $updatedCategory->id,
                'title' => $updatedCategory->title,
                'parent_id' => $updatedCategory->parent_id,
                'code' => $updatedCategory->code,
                'description' => $updatedCategory->description,
            ] : null,
        ]);
    }

    /**
     * Creates a new product using submitted POST data and optional uploaded image.
     *
     * @param HttpRequest $request The HTTP request object containing POST data and uploaded files.
     *
     * @return JsonResponse HTTP response object for redirection after creation.
     */
    public function addCategory(HttpRequest $request): JsonResponse
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $categoryDTO = $this->collectFormData($data, $request);

        $returnDTO = $this->service->create($categoryDTO);

        $success = true;
        if ($returnDTO == null) {
            $success = false;
        }

        $status = $success ? 'success' : 'error';
        $message = $success ? 'Product added successfully.' : 'Failed to add product.';

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $returnDTO ? [
                'id' => $returnDTO->id,
                'title' => $returnDTO->title,
                'parent_id' => $returnDTO->parent_id,
                'code' => $returnDTO->code,
                'description' => $returnDTO->description,
            ] : null,
        ]);
    }

    /**
     * Build DTO from JSON (if empty, fallback to getHttpPost)
     *
     * @param array $data
     * @param HttpRequest $request
     *
     * @return CategoryDTO containing all form entries.
     */
    private function collectFormData(array $data, HttpRequest $request): CategoryDTO
    {
        return new CategoryDTO(
            id: $data['id'] ?? $request->getHttpPost('id', 0),
            title: $data['title'] ?? $request->getHttpPost('title', ''),
            parent_id: $data['parent_id'] ?? $request->getHttpPost('parent', null),
            code: $data['code'] ?? $request->getHttpPost('code', ''),
            description: $data['description'] ?? $request->getHttpPost('description', ''),
        );
    }
}
