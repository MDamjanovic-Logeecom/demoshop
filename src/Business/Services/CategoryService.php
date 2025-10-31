<?php

namespace Demoshop\Local\Business\Services;

use Demoshop\Local\Business\Interfaces\Repository\ICategoryRepository;
use Demoshop\Local\Business\Interfaces\Service\ICategoryService;

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
}
