<?php

namespace Demoshop\Local\DTO;

class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?int $parent_id,
        public readonly string $code,
        public readonly ?string $description
    ) {
    }
}