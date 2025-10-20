<?php

namespace Demoshop\Local\Data;

use Demoshop\Local\Models\Product;
use Exception;
use PDO;
use PDOException;

/**
 * Class ProductRepository
 *
 * Handles database operations related to products.
 * Implements the IProductRepository interface for CRUD operations.
 */
class ProductRepository implements IProductRepository
{
    /**
     * @var PDO The PDO instance for database connection.
     */
    private PDO $pdo;

    /**
     * ProductRepository constructor.
     *
     * Establishes a connection to the MySQL database using PDO.
     * Configuration details (host, database, user, password, charset) are defined here.
     * Throws an exception and stops execution if the connection fails.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
        $products = [];

        try {
            $stmt = $this->pdo->query("SELECT * FROM products");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $products[] = $this->mapRowToProduct($row);
            }
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
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
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE SKU = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        return $this->mapRowToProduct($row);
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
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE SKU = :sku");
            $stmt->bindParam(':sku', $sku);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            echo "Delete failed: " . $e->getMessage();

            return false;
        }
    }

    /**
     * Update an existing product in the database.
     *
     * @param Product $product The product object containing updated data.
     *
     * @return bool True if update succeeds, false if an exception occurs.
     */
    public function update(Product $product): bool
    {
        try {
            // Base SQL query (without image yet)
            $sql = "UPDATE products
                SET Title = :title,
                    Brand = :brand,
                    Category = :category,
                    Dscrptn = :sdescription,
                    LDscrptn = :ldescription,
                    Enabled = :enabled,
                    Price = :price";

            $params = [
                ':title' => $product->getTitle(),
                ':brand' => $product->getBrand(),
                ':category' => $product->getCategory(),
                ':sdescription' => $product->getShortDescription(),
                ':ldescription' => $product->getLongDescription(),
                ':enabled' => (int)$product->isEnabled(),
                ':sku' => $product->getSku(),
                ':price' => $product->getPrice()
            ];

            $imageData = $product->getImage();

            if ($imageData !== null) {
                $sql .= ", Image = :image";
                $params[':image'] = $imageData;
            }

            $sql .= " WHERE SKU = :sku";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;

        } catch (PDOException $e) {
            echo "Update failed: " . $e->getMessage();

            return false;
        }
    }

    /**
     * Insert a new product into the database.
     *
     * @param Product $product The product object to insert.
     *
     * @return bool True on success, false on failure.
     */
    public function create(Product $product): bool
    {
        try {
            $sql = "INSERT INTO products (SKU, Title, Brand, Category, Dscrptn, LDscrptn, Enabled, Price";

            $params = [
                ':sku' => $product->getSku(),
                ':title' => $product->getTitle(),
                ':brand' => $product->getBrand(),
                ':category' => $product->getCategory(),
                ':sdescription' => $product->getShortDescription(),
                ':ldescription' => $product->getLongDescription(),
                ':enabled' => (int)$product->isEnabled(),
                ':price' => $product->getPrice()
            ];

            // If image is uploaded, convert to BLOB and include in SQL
            if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $sql .= ", Image";
                $params[':image'] = file_get_contents($_FILES['image']['tmp_name']);
            }

            $sql .= ") VALUES (:sku, :title, :brand, :category, :sdescription, :ldescription, :enabled, :price";

            if (isset($params[':image'])) {
                $sql .= ", :image";
            }

            $sql .= ")";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;

        } catch (PDOException $e) {
            echo "Insert failed: " . $e->getMessage();

            return false;
        }
    }

    /**
     * Maps a row from the table and maps it to a Product model object.
     *
     * @param array $row from the database.
     *
     * @return Product object extracted from row.
     */
    private function mapRowToProduct(array $row): Product
    {
        $imageData = null;
        if (!empty($row['Image'])) {
            $imageData = 'data:image/jpeg;base64,' . base64_encode($row['Image']);
        }

        return new Product(
            sku: $row['SKU'],
            title: $row['Title'],
            brand: $row['Brand'],
            category: $row['Category'],
            shortDescription: $row['Dscrptn'] ?? null,
            longDescription: $row['LDscrptn'] ?? null,
            price: (float)$row['Price'],
            image: $imageData,
            enabled: (bool)$row['Enabled'],
        );
    }
}