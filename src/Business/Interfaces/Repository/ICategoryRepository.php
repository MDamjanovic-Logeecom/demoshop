<?php

namespace Demoshop\Local\Business\Interfaces\Repository;

use Demoshop\Local\DTO\CategoryDTO;

/**
 * Interface for category repositories
 */
interface ICategoryRepository
{
    /**
     * Gets all categories from the database
     *
     * @return array
     */
    public function getAll(): array;

    /**
     * Update an existing category
     *
     * @param CategoryDTO $category
     *
     * @return CategoryDTO|null
     */
    public function update(CategoryDTO $category): ?CategoryDTO;

    /**
     * Create a new category
     *
     * @param CategoryDTO $category
     *
     * @return CategoryDTO|null
     */
    public function create(CategoryDTO $category): ?CategoryDTO;

    /**
     * Delete a category by code
     *
     * @param string $code
     *
     * @return bool
     */
    public function deleteByCode(string $code): bool;

    /**
     * gets all descendants of a parent
     *
     * @param int $parentId
     *
     * @return array
     */
    public function getAllDescendants(int $parentId): array;
}
