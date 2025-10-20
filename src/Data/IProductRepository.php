<?php
namespace Demoshop\Local\Data;

use Demoshop\Local\Models\Product;

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
     * @return Product The corresponding Product object.
     */
    public function getBySKU(string $sku): Product;

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
     * @param Product $product The Product object to insert.
     *
     * @return bool True if creation was successful, false otherwise.
     */
    public function create(Product $product): bool;

    /**
     * Update an existing product in the database.
     *
     * @param Product $product The Product object to update.
     *
     * @return bool True if update was successful, false otherwise.
     */
    public function update(Product $product): bool;
}
