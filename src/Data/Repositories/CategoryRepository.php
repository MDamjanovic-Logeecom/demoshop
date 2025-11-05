<?php

namespace Demoshop\Local\Data\Repositories;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Data\Models\EloquentCategory;
use Demoshop\Local\DTO\CategoryDTO;

class CategoryRepository implements ICategoryRepository
{
    /**
     * get all categorise from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $eloquentCategories = EloquentCategory::all();
        $categories = [];

        foreach ($eloquentCategories as $currentCategory) {
            $categories[] = $this->mapEloquentToDTO($currentCategory);
        }

        return $categories;
    }

    /**
     * Get a single category by its code.
     *
     * @param string $code of the category to fetch.
     *
     * @return CategoryDTO|null The product object corresponding to the given code.
     */
    public function getByCode(string $code): ?CategoryDTO
    {
        $eloquentCategory = EloquentCategory::where('code', $code)->first();

        if (!$eloquentCategory) {
            return null;
        }

        return $this->mapEloquentToDTO($eloquentCategory);
    }

    /**
     * Update an existing category in the database.
     *
     * @param CategoryDTO $category
     * @return CategoryDTO|null whether executed successfully.
     */
    public function update(CategoryDTO $category): ?CategoryDTO
    {
        $eloquentCategory = EloquentCategory::where('code', $category->code)->first();

        if (!$eloquentCategory) {
            return null;
        }

        $eloquentCategory->title = $category->title;
        $eloquentCategory->code = $category->code;
        $eloquentCategory->description = $category->description;

        if ($eloquentCategory->save()) {
            return $this->mapEloquentToDTO($eloquentCategory);
        }

        return null;
    }

    /**
     * Insert a new category into the database.
     *
     * @param CategoryDTO $category The product object to insert.
     *
     * @return CategoryDTO|null whether executed successfully.
     */
    public function create(CategoryDTO $category): ?CategoryDTO
    {
        $eloquentCategory = new EloquentCategory([
            'title' => $category->title,
            'parent_id' => $category->parent_id,
            'code' => $category->code,
            'description' => $category->description,
        ]);

        if ($eloquentCategory->save()) {
            return $this->mapEloquentToDTO($eloquentCategory);
        }

        return null;
    }

    /**
     * Delete a category from the database by its SKU.
     *
     * @param string $code of the category to delete.
     *
     * @return bool True if the category was deleted, false on failure or if not found.
     */
    public function deleteByCode(string $code): bool
    {
        $eloquentCategory = EloquentCategory::where('code', $code)->first();

        if (!$eloquentCategory) {
            return false;
        }

        return $eloquentCategory->delete();
    }

    /**
     * Get first level of descendants of a given category
     *
     * @param int $parentId
     *
     * @return array
     */
    public function getChildren(int $parentId): array
    {
        $eloquentChildren = EloquentCategory::where('parent_id', $parentId)->get();

        $children = [];
        foreach ($eloquentChildren as $child) {
            $children[] = $this->mapEloquentToDTO($child);
        }

        return $children;
    }

    /**
     * Get all descendants recursively of a given category
     *
     * @param int $parentId
     *
     * @return array
     */
    public function getAllDescendants(int $parentId): array
    {
        $descendants = [];
        $children = $this->getChildren($parentId);

        foreach ($children as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->getAllDescendants($child->id));
        }

        return $descendants;
    }

    /**
     * Maps a row from the table and maps it to a Category model object.
     *
     * @param EloquentCategory $eloquentCategory from the database.
     *
     * @return CategoryDTO object extracted from row.
     */
    private function mapEloquentToDTO(EloquentCategory $eloquentCategory): CategoryDTO
    {
        return new CategoryDTO(
            id: $eloquentCategory->id,
            title: $eloquentCategory->title,
            parent_id: $eloquentCategory->parent_id,
            code: $eloquentCategory->code,
            description: $eloquentCategory->description,
        );
    }
}
