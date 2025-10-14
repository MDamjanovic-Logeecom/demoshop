<?php
/** @var Product $product */ // Controller passes the product (bc of it requiring this page -> this page is part of its scope)
/** @var PDO $pdo */
require 'db_connect.php'; // include db connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="/presentation/public/css/add_product.css">
</head>
<body>
<div class="container">
    <h2>Product Details</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="left-side">
                <div class="form-group">
                    <label>SKU:</label>
                    <input type="text" name="sku" value="<?= htmlspecialchars($product->getSKU()) ?>">
                </div>
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($product->getTitle()) ?>">
                </div>
                <div class="form-group">
                    <label>Brand:</label>
                    <input type="text" name="brand" value="<?= htmlspecialchars($product->getBrand()) ?>">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category">
                        <option value="Laptop" <?= $product->getCategory() === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product->getPrice()) ?>">
                </div>
                <div class="form-group">
                    <label>Short Description:</label>
                    <textarea name="short_description"><?= htmlspecialchars($product->getShortDescription() ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description"><?= htmlspecialchars($product->getLongDescription() ?? '') ?></textarea>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="enabled" <?= $product->isEnabled() ? 'checked' : '' ?>>
                    <label>Enabled in Shop</label>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="featured" <?= $product->isFeatured() ? 'checked' : '' ?>>
                    <label>Featured</label>
                </div>
            </div>

            <div class="right-side">
                <!-- Image preview -->
                <img id="preview"
                     src="<?= !empty($product->getImage()) ? $product->getImage() : 'placeholder.jpg' ?>"
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

