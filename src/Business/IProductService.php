<?php

namespace Demoshop\Local\Business;

use Demoshop\Local\DTO\ProductDTO;

/**
 * Interface IProductService
 *
 * Defines the contract for service classes handling product operations.
 * Any class implementing this interface must provide methods for
 * CRUD operations on products.
 */
interface IProductService
{
    /**
     * Retrieve all products.
     *
     * @return array An array of ProductDTO objects.
     */
    public function getAll(): array;

    /**
     * Retrieve a product by its SKU.
     *
     * @param string $sku The SKU of the product to retrieve.
     *
     * @return ProductDTO|null The product object corresponding to the SKU.
     */
    public function getBySKU(string $sku): ?ProductDTO;

    /**
     * Delete a product by its SKU.
     *
     * @param string $sku The SKU of the product to delete.
     *
     * @return bool True if the product was deleted, false otherwise.
     */
    public function deleteBySKU(string $sku): bool;

    /**
     * Create a new product.
     *
     * @param ProductDTO $productDTO
     * @return ProductDTO|null DTO on success, null on failure.
     */
    public function create(ProductDTO $productDTO): ?ProductDTO;

    /**
     * Update an existing product.
     *
     * @param ProductDTO $productDTO
     * @return ProductDTO|null DTO on success, null on failure.
     */
    public function update(ProductDTO $productDTO): ?ProductDTO;
}
