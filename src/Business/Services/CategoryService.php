<?php

namespace Demoshop\Local\Business\Services;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Business\Interfaces\Service\ICategoryService;
use Demoshop\Local\DTO\CategoryDTO;

class CategoryService implements ICategoryService
{
    private ICategoryRepository $repository;

    /**
     * @param ICategoryRepository $repository
     */
    public function __construct(ICategoryRepository $repository)
    {
        $this->repository = $repository;
    }

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
}
