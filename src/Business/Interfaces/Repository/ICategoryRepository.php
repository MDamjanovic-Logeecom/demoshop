<?php

namespace Demoshop\Local\Business\Interfaces\Repository;

use Demoshop\Local\DTO\CategoryDTO;

interface ICategoryRepository
{
    public function getAll(): array;
    public function update(CategoryDTO $category): ?CategoryDTO;
    public function create(CategoryDTO $category): ?CategoryDTO;
    public function deleteByCode(string $code): bool;
}
