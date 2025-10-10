<?php
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

//------------------------------------- Database interactions:

// Fetch all products in db
function getAllProducts() {
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

            $products[] = [
                'Title' => $row['Title'],
                'SKU' => $row['SKU'],
                'Brand' => $row['Brand'],
                'Category' => $row['Category'],
                'Short description' => $row['Dscrptn'],
                'description' => $row['LDscrptn'],
                'Price' => (float)$row['Price'],
                'Enabled' => (bool)$row['Enabled'],
                'Image' => $imageData
            ];
        }

    } catch (PDOException $e) {
        echo "Query failed: " . $e->getMessage();
    }

    return $products;
}

// Get single product by SKU (primary key)
function getProductBySKU($sku) {
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

    return [
        'Title' => $row['Title'],
        'SKU' => $row['SKU'],
        'Brand' => $row['Brand'],
        'Category' => $row['Category'],
        'Dscrptn' => $row['Dscrptn'],
        'LDscrptn' => $row['LDscrptn'],
        'Price' => (float)$row['Price'],
        'Enabled' => (bool)$row['Enabled'],
        'Image' => $imageData
    ];
}

// Adds a product
function addProduct($sku, $title, $brand, $category, $sdescription = null, $enabled = null, $imageFile = null, $ldescription = null, $price = null) {
    global $pdo;

    try {
        // Base SQL query
        $sql = "INSERT INTO products (SKU, Title, Brand, Category, Dscrptn, LDscrptn, Enabled, Price";

        // Base SQL query (without image yet)
        $params = [
            ':sku' => $sku,
            ':title' => $title,
            ':brand' => $brand,
            ':category' => $category,
            ':sdescription' => $sdescription,
            ':ldescription' => $ldescription,
            ':enabled' => $enabled,
            ':price' => $price
        ];

        // If image is uploaded, convert to BLOB and include in SQL
        if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
            $sql .= ", Image";
            $params[':image'] = file_get_contents($imageFile['tmp_name']);
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
function deleteProductBySKU($sku) {
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


// Edits a product
function editProduct($sku, $title, $brand, $category, $sdescription, $ldescription, $enabled, $imageFile = null) { // imageFile parameter is optional (not defined as null)
    global $pdo;

    try {
        // Base SQL query (without image yet)
        $sql = "UPDATE products 
                SET Title = :title, 
                    Brand = :brand, 
                    Category = :category, 
                    Dscrptn = :sdescription, 
                    LDscrptn = :ldescription, 
                    Enabled = :enabled";

        $params = [
            ':title' => $title,
            ':brand' => $brand,
            ':category' => $category,
            ':sdescription' => $sdescription,
            ':ldescription' => $ldescription,
            ':enabled' => $enabled,
            ':sku' => $sku
        ];

        // If image is uploaded, convert to BLOB and include in SQL
        if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
            $imageData = file_get_contents($imageFile['tmp_name']);
            $sql .= ", Image = :image";
            $params[':image'] = $imageData;
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
