<?php

namespace Demoshop\Local\Business\Interfaces\Service;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\DTO\CategoryDTO;

interface ICategoryService
{
    /**
     * Retrieve all categories.
     *
     * @return array An array of CategoryDTO objects.
     */
    public function getAll(): array;

    /**
     * Updates an existing category using the provided form data.
     *
     * @param CategoryDTO $categoryDTO Object of submitted category data
     *
     * @return CategoryDTO|null DTO if the category was successfully updated, null otherwise.
     */
    public function update(CategoryDTO $categoryDTO): ?CategoryDTO;

    /**
     * Creates a new category.
     *
     * @param CategoryDTO $categoryDTO Object.
     *
     * @return CategoryDTO|null DTO if the product was successfully created, null otherwise.
     */
    public function create(CategoryDTO $categoryDTO): ?CategoryDTO;

    /**
     * Deletes a category by its code.
     *
     * @param string $code The SKU of the product to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteByCode(string $code): bool;
}
