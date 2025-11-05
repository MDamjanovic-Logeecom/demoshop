<?php

namespace Demoshop\Local\Data\Repositories;

use Demoshop\Local\Business\Interfaces\Repository\IProductRepository;
use Demoshop\Local\Data\Models\EloquentProduct;
use Demoshop\Local\DTO\ProductDTO;

/**
 * Class ProductRepository
 *
 * Handles database operations related to products.
 * Implements the IProductRepository interface for CRUD operations.
 */
class ProductRepository implements IProductRepository
{
    /**
     * Empty ProductRepository constructor.
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
     * @return ProductDTO[] Array of ProductDTO objects.
     */
    public function getAll(): array
    {
        $eloquentProducts = EloquentProduct::all(); // Fetch all records
        $products = [];

        foreach ($eloquentProducts as $currentProduct) {
            $products[] = $this->mapEloquentToDTO($currentProduct);
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
     * @return ProductDTO|null The product object corresponding to the given SKU.
     */
    public function getBySKU(string $sku): ?ProductDTO
    {
        $eloquentProduct = EloquentProduct::find($sku);

        if (!$eloquentProduct) {
            return null;
        }

        return $this->mapEloquentToDTO($eloquentProduct);
    }

    /**
     * Receives filter parameters, returns filtered products
     *
     * @param string $search
     * @param bool $enabledOnly
     * @param string|null $titleAsc
     * @param string|null $priceAsc
     *
     * @return array
     */
    public function filterProducts(string $search, bool $enabledOnly, ?string $titleAsc, ?string $priceAsc): array
    {
        $query = EloquentProduct::query();

        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%")
                    ->orWhere('category', 'like', "%$search%");
            });
        }

        if ($enabledOnly) {
            $query->where('enabled', true);
        }

        if ($titleAsc !== null) {
            $query->orderBy('title', $titleAsc === 'true' ? 'asc' : 'desc');
        }

        if ($priceAsc !== null) {
            $query->orderBy('price', $priceAsc === 'true' ? 'asc' : 'desc');
        }

        $eloquentProducts = $query->get();

        return array_map([$this, 'mapEloquentToDTO'], $eloquentProducts->all());
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
     * @param ProductDTO $product The product object containing updated data.
     *
     * @return ProductDTO|null whether executed successfully.
     */
    public function update(ProductDTO $product): ?ProductDTO
    {
        $eloquentProduct = EloquentProduct::find($product->sku);

        if (!$eloquentProduct) {
            return null;
        }

        $eloquentProduct->Title = $product->title;
        $eloquentProduct->Brand = $product->brand;
        $eloquentProduct->Category = $product->category;
        $eloquentProduct->Dscrptn = $product->shortDescription;
        $eloquentProduct->LDscrptn = $product->description;
        $eloquentProduct->Enabled = $product->enabled;
        $eloquentProduct->Price = $product->price;

        if ($product->image !== null) {
            $eloquentProduct->Image = $product->image;
        }

        if ($eloquentProduct->save()) {
            return $this->mapEloquentToDTO($eloquentProduct);
        }

        return null;
    }

    /**
     * Insert a new product into the database.
     *
     * @param ProductDTO $product The product object to insert.
     *
     * @return ProductDTO|null whether executed successfully.
     */
    public function create(ProductDTO $product): ?ProductDTO
    {
        $eloquentProduct = new EloquentProduct([
            'SKU' => $product->sku,
            'Title' => $product->title,
            'Brand' => $product->brand,
            'Category' => $product->category,
            'Dscrptn' => $product->shortDescription,
            'LDscrptn' => $product->description,
            'Price' => $product->price,
            'Enabled' => $product->enabled,
            'Image' => $product->image,
        ]);

        if ($eloquentProduct->save()) {
            return $this->mapEloquentToDTO($eloquentProduct);
        }

        return null;
    }

    /**
     * Return how many products have a given category code
     *
     * @param string $categoryCode
     *
     * @return int
     */
    public function countByCategory(string $categoryCode): int
    {
        return EloquentProduct::where('Category', $categoryCode)->count();
    }

    /**
     * Maps a row from the table and maps it to a Product model object.
     *
     * @param EloquentProduct $eloquentProduct from the database.
     *
     * @return ProductDTO object extracted from row.
     */
    private function mapEloquentToDTO(EloquentProduct $eloquentProduct): ProductDTO
    {
        $imageData = $eloquentProduct->Image ? 'data:image/jpeg;base64,' . base64_encode($eloquentProduct->Image) : null;

        return new ProductDTO(
            sku: $eloquentProduct->SKU,
            title: $eloquentProduct->Title,
            brand: $eloquentProduct->Brand,
            category: $eloquentProduct->Category,
            shortDescription: $eloquentProduct->Dscrptn,
            description: $eloquentProduct->LDscrptn,
            enabled: $eloquentProduct->Enabled,
            price: $eloquentProduct->Price,
            image: $imageData,
        );
    }
}
