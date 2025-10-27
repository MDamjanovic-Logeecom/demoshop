<?php
namespace Demoshop\Local\Business\Interfaces\Repository;

use Demoshop\Local\DTO\ProductDTO;

/**
 * Interface IProductRepository
 *
 * Defines the contract for product repository operations.
 */
interface IProductRepository
{
    /**
     * Retrieve all products from the database.
     *
     * @return array An array of Product objects.
     */
    public function getAll(): array;

    /**
     * Retrieve a single product by SKU.
     *
     * @param string $sku The SKU of the product to retrieve.
     *
     * @return ProductDTO|null The corresponding Product object.
     */
    public function getBySKU(string $sku): ?ProductDTO;

    /**
     * Delete a product by SKU.
     *
     * @param string $sku The SKU of the product to delete.
     *
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteBySKU(string $sku): bool;

    /**
     * Insert a new product into the database.
     *
     * @param ProductDTO $product The Product object to insert.
     *
     * @return ProductDTO|null DTO if creation was successful.
     */
    public function create(ProductDTO $product): ?ProductDTO;

    /**
     * Update an existing product in the database.
     *
     * @param ProductDTO $product The Product object to update.
     *
     * @return ProductDTO|null if update was successful.
     */
    public function update(ProductDTO $product): ?ProductDTO;
}
