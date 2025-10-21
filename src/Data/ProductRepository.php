<?php

namespace Demoshop\Local\Data;

use Demoshop\Local\Infrastructure\Eloquent\EloquentProduct;
use Demoshop\Local\Models\Product;
use PDO;

/**
 * Class ProductRepository
 *
 * Handles database operations related to products.
 * Implements the IProductRepository interface for CRUD operations.
 */
class ProductRepository implements IProductRepository
{
    /**
     * ProductRepository constructor.
     *
     * Establishes a connection to the MySQL database using PDO.
     * Configuration details (host, database, user, password, charset) are defined here.
     * Throws an exception and stops execution if the connection fails.
     */
    public function __construct()
    {
    }

    /**
     * Get all products from the database.
     *
     * Retrieves all rows from the 'products' table and converts them into Product objects.
     * If the 'Image' column contains binary data (BLOB), it is converted to base64.
     *
     * @return Product[] Array of Product objects.
     */
    public function getAll(): array
    {
        $eloquentProducts = EloquentProduct::all(); // Fetch all records
        $products = [];

        foreach ($eloquentProducts as $ep) {
            $products[] = $this->mapEloquentToModel($ep);
        }

        return $products;
    }

    /**
     * Get a single product by its SKU.
     *
     * Fetches one product row from the database matching the given SKU and converts it into a Product object.
     * Handles missing fields and converts the 'Image' column from BLOB to base64 if present.
     *
     * @param string $sku SKU of the product to fetch.
     *
     * @return Product The product object corresponding to the given SKU.
     */
    public function getBySKU(string $sku): Product
    {
        $eloquentProduct = EloquentProduct::find($sku);

        if (!$eloquentProduct) {
            return false; // Product not found
        }

        return $this->mapEloquentToModel($eloquentProduct);
    }

    /**
     * Delete a product from the database by its SKU.
     *
     * @param string $sku SKU of the product to delete.
     *
     * @return bool True if the product was deleted, false on failure or if not found.
     */
    public function deleteBySKU(string $sku): bool
    {
        $eloquentProduct = EloquentProduct::find($sku);

        if (!$eloquentProduct) {
            return false;
        }

        return $eloquentProduct->delete();
    }

    /**
     * Update an existing product in the database.
     *
     * @param Product $product The product object containing updated data.
     *
     * @return bool whether executed successfully.
     */
    public function update(Product $product): bool
    {
        $eloquentProduct = EloquentProduct::find($product->getSKU());

        if (!$eloquentProduct) {
            return false;
        }

        $eloquentProduct->Title = $product->getTitle();
        $eloquentProduct->Brand = $product->getBrand();
        $eloquentProduct->Category = $product->getCategory();
        $eloquentProduct->Dscrptn = $product->getShortDescription();
        $eloquentProduct->LDscrptn = $product->getLongDescription();
        $eloquentProduct->Enabled = $product->isEnabled();
        $eloquentProduct->Price = $product->getPrice();

        if ($product->getImage() !== null) {
            $eloquentProduct->Image = $product->getImage();
        }

        return $eloquentProduct->save();
    }

    /**
     * Insert a new product into the database.
     *
     * @param Product $product The product object to insert.
     *
     * @return bool whether executed successfully.
     */
    public function create(Product $product): bool
    {
        $eloquentProduct = new EloquentProduct([
            'SKU' => $product->getSku(),
            'Title' => $product->getTitle(),
            'Brand' => $product->getBrand(),
            'Category' => $product->getCategory(),
            'Dscrptn' => $product->getShortDescription(),
            'LDscrptn' => $product->getLongDescription(),
            'Price' => $product->getPrice(),
            'Enabled' => $product->isEnabled(),
            'Image' => $product->getImage()
        ]);

        return $eloquentProduct->save();
    }

    /**
     * Maps a row from the table and maps it to a Product model object.
     *
     * @param EloquentProduct $ep from the database.
     *
     * @return Product object extracted from row.
     */
    private function mapEloquentToModel(EloquentProduct $ep): Product
    {
        $imageData = null;
        if (!empty($ep->Image)) {
            $imageData = 'data:image/jpeg;base64,' . base64_encode($ep->Image);
        }

        return new Product(
            sku: $ep->SKU,
            title: $ep->Title,
            brand: $ep->Brand,
            category: $ep->Category,
            shortDescription: $ep->Dscrptn,
            longDescription: $ep->LDscrptn,
            price: $ep->Price,
            image: $imageData,
            enabled: (bool)$ep->Enabled
        );
    }
}
