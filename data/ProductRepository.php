<?php

require_once __DIR__ . '/IRepository.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class ProductRepository implements IRepository {
    //TODO: PDO is going to have to be gifted through the db_connect file
    private PDO $pdo;
    //TODO: test all of this, buddy-boii (THROUGH SERVICE!)

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // TODO: Get all products
    public function getAll(): array {
        $products = [];

        try{
            $stmt = $this->pdo->query("SELECT * FROM products");

            while ($row = $stmt->fetch()) {

                // Block for image conversion
                $imageData = null;
                if (!empty($row['Image'])) {
                    // BLOB -> base64
                    $base64 = base64_encode($row['Image']);
                    // MIME type
                    $imageData = "data:image/jpeg;base64," . $base64;
                }

                $sku   = isset($row['SKU']) ? (string)$row['SKU'] : '';
                $title = isset($row['Title']) ? (string)$row['Title'] : '';
                $brand = isset($row['Brand']) ? (string)$row['Brand'] : '';
                $category = isset($row['Category']) ? (string)$row['Category'] : '';
                $sdesc = isset($row['Dscrptn']) ? (string)$row['Dscrptn'] : null;
                $ldesc = isset($row['LDscrptn']) ? (string)$row['LDscrptn'] : null;
                $price = isset($row['Price']) ? (float)$row['Price'] : 0.0;
                $enabled = isset($row['Enabled']) ? (bool)$row['Enabled'] : false;

                //$product = new Product();
                try {
                    $product = new Product($sku, $title, $brand, $category, $sdesc, $ldesc, $price, $imageData, $enabled);
                }catch (Exception $e) {
                    echo $e->getMessage();
                }

                $products[] = $product;
            }

        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }

        return $products;
    }

    // TODO: Get single product by SKU
    public function getBySKU(string $sku) : Product {

        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE SKU = :sku");
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        // Convert BLOB to Base64 for image if it exists
        $imageData = null;
        if (!empty($row['Image'])) {
            $imageData = "data:image/jpeg;base64," . base64_encode($row['Image']);
        }

        $sku   = isset($row['SKU']) ? (string)$row['SKU'] : '';
        $title = isset($row['Title']) ? (string)$row['Title'] : '';
        $brand = isset($row['Brand']) ? (string)$row['Brand'] : '';
        $category = isset($row['Category']) ? (string)$row['Category'] : '';
        $sdesc = isset($row['Dscrptn']) ? (string)$row['Dscrptn'] : null;
        $ldesc = isset($row['LDscrptn']) ? (string)$row['LDscrptn'] : null;
        $price = isset($row['Price']) ? (float)$row['Price'] : 0.0;
        $enabled = isset($row['Enabled']) ? (bool)$row['Enabled'] : false;

        $product = new Product($sku, $title, $brand, $category, $sdesc, $ldesc, $price, $imageData, $enabled);

        return $product;
    }

    // Delete product by SKU
    public function deleteBySKU(string $sku): bool {

        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE SKU = :sku");
            $stmt->bindParam(':sku', $sku);
            $stmt->execute();

            // Check if any row was affected
            if ($stmt->rowCount() > 0) {
                return true; // Successfully deleted
            } else {
                return false; // SKU not found
            }
        } catch (PDOException $e) {
            echo "Delete failed: " . $e->getMessage();
            return false;
        }
    }

    // TODO: Update Product by SKU
    public function update(Product $product): bool {

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
                ':enabled' => $product->isEnabled(),
                ':sku' => $product->getSku(),
                ':price' => $product->getPrice()
            ];

            $imageFile = $product->getImage();

            // Checking if image is uploaded, already there of empty
            if ($imageFile) {
                if (strpos($imageFile, 'data:image') === 0) {
                    // Base64 image -> image already exists, no upload
                    [$meta, $base64] = explode(',', $imageFile);
                    $imageData = base64_decode($base64);

                } elseif (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                    // New uploaded image
                    $imageData = file_get_contents($_FILES['image']['tmp_name']);

                } else {
                    $imageData = null;
                }

                if ($imageData !== null) {
                    $sql .= ", Image = :image";
                    $params[':image'] = $imageData;
                }
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

    // TODO: Create new product
    public function create(Product $product): bool {

        try {
            // Base SQL query
            $sql = "INSERT INTO products (SKU, Title, Brand, Category, Dscrptn, LDscrptn, Enabled, Price";

            // Base SQL query (without image yet)
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

            $image = $product->getImage();

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
}