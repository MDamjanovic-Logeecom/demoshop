<?php

namespace Demoshop\Local\Data\Repositories;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Data\Models\EloquentCategory;
use Demoshop\Local\DTO\CategoryDTO;

class CategoryRepository implements ICategoryRepository
{

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
     * Update an existing category in the database.
     *
     * @param CategoryDTO $category
     * @return CategoryDTO|null whether executed successfully.
     */
    public function update(CategoryDTO $category): ?CategoryDTO
    {
        $eloquentCategory = EloquentCategory::find($category->code);

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
