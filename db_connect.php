<?php

//use models\Product;

require './models/Product.php';

$host = 'localhost';  // MySQL server on windows from WSL
$db   = 'milos';       // DB name
$user = 'root';       // MySQL user
$pass = 'root';   // user password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    //echo "Connected to demo_shop database!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

//TODO: MOVE ALL OF THIS INTO ProductRepository!!!!!!!!!!!!!!
//------------------------------------- Database interactions:

// Fetch all products in db
function getAllProducts(): array { //TODO: to be tested
    global $pdo;

    $products = [];
    try{
        $stmt = $pdo->query("SELECT * FROM products");

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

// Get single product by SKU (primary key)
function getProductBySKU($sku): Product {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM products WHERE SKU = :sku");
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

// Adds a product
function addProduct($product): bool {
    global $pdo;

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

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return true;
    } catch (PDOException $e) {
        echo "Insert failed: " . $e->getMessage();
        return false;
    }
}

// Delete a product
function deleteProductBySKU($sku): bool {
    global $pdo;

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE SKU = :sku");
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

//Edits a product
function editProduct($product): bool { // imageFile parameter is optional (not defined as null)
    global $pdo;

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

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return true;

    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
        return false;
    }
}

?>
