<?php

namespace Demoshop\Local\Business\Interfaces\Service;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;

interface ICategoryService
{
    /**
     * Retrieve all categories.
     *
     * @return array An array of CategoryDTO objects.
     */
    public function getAll(): array;
}
