<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="css/add_product.css">
</head>
<body>
<div class="container">
    <h2>Product Details</h2>
    <form action="index.php?page=add" method="post" enctype="multipart/form-data">
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
                <input type="file" id="file" name="image" style="display:none;" accept="image/*"
                       onchange="handleFileSelect(event)">

                <button type="button" onclick="triggerFileInput('file')"> Upload</button>
            </div>
        </div>
        <div class="right-side" style=" text-align: right;">
            <button type="submit">Save</button>
        </div>
    </form>
</div>
<script src="js/add_edit_form.js"></script>
</body>
</html>


