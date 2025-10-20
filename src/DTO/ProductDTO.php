<?php

namespace Demoshop\Local\DTO;

/**
 * DTO Object for transferring data between the Presentation and Business layers.
 *
 * Set to be immutable - once constructed, cannot be changed
 */
class ProductDTO
{
    /**
     * @param string $sku
     * @param string $title
     * @param string|null $brand
     * @param string|null $category
     * @param string|null $shortDescription
     * @param string|null $description
     * @param bool|null $enabled
     * @param float|null $price
     * @param string|null $image
     */
    public function __construct(
        public readonly string $sku,
        public readonly string $title,
        public readonly ?string $brand,
        public readonly ?string $category,
        public readonly ?string $shortDescription,
        public readonly ?string $description,
        public readonly ?bool $enabled,
        public readonly ?float $price,
        public readonly ?string $image = null,
    ) {
    }
}
