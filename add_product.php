<?php
/** @var PDO $pdo */
require 'db_connect.php'; // include db connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture submitted data
    $sku = isset($_POST['sku']) ? $_POST['sku'] : ''; // isset: if the field exists, use the value; if not -> use ''
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $sdescription = isset($_POST['short_description']) ? $_POST['short_description'] : '';
    $ldescription = isset($_POST['description']) ? $_POST['description'] : '';
    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $imageFile = isset($_FILES['image']) ? $_FILES['image'] : null; // Capture uploaded image file if any
    $price = (float)(isset($_POST['price']) ? $_POST['price'] : 0.0);

    if ($sku == null || $title == null){
        echo "<script>
            alert('SKU and Title are required.');
            window.location.href = 'index.php';
          </script>";
    }

    if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) { // If img uploaded -> check if valid b4 sending to db
        if(imageIsOkay($imageFile)) {

            $imageData = file_get_contents($imageFile['tmp_name']);

            $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, $imageData, $enabled);
            $success = addProduct($product);

        }
    }else { // If no img upload, nothing to validate
        $product = new Product($sku, $title, $brand, $category, $sdescription, $ldescription, $price, null, $enabled);
        $success = addProduct($product);
    }

    if ($success) {
        echo "<script>
            alert('Product added successfully.');
            window.location.href = 'index.php';
          </script>";
    } else {
        echo "<script>
            alert('Error adding product.');
            window.location.href = 'index.php';
          </script>";
    }

}

function imageIsOkay($imageFile) {
    // Ensure a file was actually uploaded
    if (!isset($imageFile['tmp_name']) || !is_uploaded_file($imageFile['tmp_name'])) {
        echo "<script>alert('No valid image file uploaded.');</script>";
        return false;
    }

    // Get image dimensions and validate
    $info = @getimagesize($imageFile['tmp_name']);
    if ($info === false) {
        echo "<script>
            alert('Uploaded file is not a valid image.');
            window.location.href = 'index.php';
            </script>";
        return false;
    }

    $width = $info[0];
    $height = $info[1];
    $ratio = $width / $height;

    // Minimum width check
    if ($width < 600) {
        echo "<script>
            alert('Image width must be at least 600px.');
            window.location.href = 'index.php';
            </script>";
        return false;
    }

    // Aspect ratio check (4:3 - 16:9)
    if ($ratio < (4/3) || $ratio > (16/9)) {
        echo "<script>
            alert('Image aspect ratio must be between 4:3 and 16:9.');
            window.location.href = 'index.php';
            </script>";
        return false;
    }

    return true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: #fff;
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-grid {
            display: flex;
            gap: 40px;
        }

        .left-side {
            flex: 2;
        }

        .right-side {
            flex: 1;
            text-align: center;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-group label {
            width: 150px;
            text-align: right;
            margin-right: 10px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            flex: 1;
            padding: 6px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .checkbox-group label {
            margin-left: 8px;
        }

        img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        td.button-upload button,
        .right-side button {
            padding: 4px 8px;
            cursor: pointer;
            background-color: white;
            border-color: #cccccc;
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Product Details</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="left-side">
                <div class="form-group">
                    <label>SKU:</label>
                    <input type="text" name="sku" value="">
                </div>
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" value="">
                </div>
                <div class="form-group">
                    <label>Brand:</label>
                    <input type="text" name="brand" value="">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category">
                        <option value="Laptop" selected>Laptop</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" value="">
                </div>
                <div class="form-group">
                    <label>Short Description:</label>
                    <textarea name="short_description"> </textarea>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description"></textarea>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="enabled">
                    <label>Enabled in Shop</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="featured">
                    <label>Featured</label>
                </div>
            </div>

            <div class="right-side">
                <!-- Image preview -->
                <img id="preview"
                     src="<?= !empty($product['Image']) ? $product['Image'] : 'placeholder.jpg' ?>"
                     alt="Product Image"
                     style="min-width:300px; min-height:300px; display:block; margin-bottom:5px;">

                <!-- Hidden file input -->
                <input type="file" id="file" name="image" style="display:none;" accept="image/*" onchange="handleFileSelect(event)">

                <button type="button" onclick="document.getElementById('file').click();">
                    Upload
                </button>
            </div>
        </div>
        <div class="right-side" style=" text-align: right;">
            <button type="submit">Save</button>
        </div>
    </form>
</div>
</body>
<script>
    // JS code to save and update the preview

    //Variable to store the selected image file
    let uploadedFile = null;

    // When file is selected
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Store file in a variable
        uploadedFile = file;

        // Preview updated until "save" clicked
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
    }

</script>
</html>


