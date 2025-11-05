<?php

namespace Demoshop\Local\Business\Services;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Business\Interfaces\Repository\IProductRepository;
use Demoshop\Local\Business\Interfaces\Service\ICategoryService;
use Demoshop\Local\DTO\CategoryDTO;

class CategoryService implements ICategoryService
{
    /**
     * Category repository for reaching categories data
     *
     * @var ICategoryRepository
     */
    private ICategoryRepository $repository;
    /**
     * Product repository for reaching products data
     *
     * @var IProductRepository
     */
    private IProductRepository $productRepository;

    /**
     * @param ICategoryRepository $repository
     * @param IProductRepository $productRepository
     */
    public function __construct(ICategoryRepository $repository, IProductRepository $productRepository)
    {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get all categories from database
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    /**
     * Updates an existing category using the provided form data.
     *
     * @param CategoryDTO $categoryDTO Object of submitted category data
     *
     * @return CategoryDTO|null DTO if the category was successfully updated, null otherwise.
     */
    public function update(CategoryDTO $categoryDTO): ?CategoryDTO
    {
        return $this->repository->update($categoryDTO);
    }

    /**
     * Creates a new category.
     *
     * @param CategoryDTO $categoryDTO Object.
     *
     * @return CategoryDTO|null DTO if the product was successfully created, null otherwise.
     */
    public function create(CategoryDTO $categoryDTO): ?CategoryDTO
    {
        return $this->repository->create($categoryDTO);
    }

    /**
     * Deletes a category by its code.
     *
     * @param string $code The SKU of the product to delete.
     *
     * @return bool True if deletion successful.
     */
    public function deleteByCode(string $code): bool
    {
        $category = $this->repository->getByCode($code);
        if (!$category) return false;

        // Category itself has products?
        if ($this->productRepository->countByCategory($category->code) > 0) {
            return false;
        }

        // Check for all descendants
        $subcategories = $this->repository->getAllDescendants($category->id);
        foreach ($subcategories as $sub) {
            if ($this->productRepository->countByCategory($sub->code) > 0) {
                return false;
            }
        }

        return $this->repository->deleteByCode($code);
    }
}
