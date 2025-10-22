<?php

/** @var array $products */
$products = $products ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demo Shop - Main Page</title>
    <link rel="stylesheet" href="/src/Presentation/public/css/products_list.css">
</head>
<body>

<div class="layout">
    <!-- Left-side buttons -->
    <div class="side-buttons">
        <button>Dashboard</button>
        <button>Products</button>
        <button>Product Categories</button>
        <button>Users</button>
    </div>

    <!-- Main box -->
    <div class="main-box">
        <h2>Products</h2>

        <!-- Inside buttons -->
        <div class="box-buttons">
            <div class="left-buttons">
                <button type="button" onclick="window.location.href='/admin/products/create'">Add new product</button>
                <button>Delete selected</button>
                <button>Enable selected</button>
                <button>Disable selected</button>
            </div>
            <div class="right-buttons">
                <button>Filter</button>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
            <tr>
                <th>Selected</th>
                <th>Title</th>
                <th>SKU</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Short description</th>
                <th>Price</th>
                <th>Enabled</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $index => $product): ?>
                <td class="checkbox-cell">
                    <input type="checkbox" name="select_item[]" value="<?= $index ?>">
                </td>
                <td><?= htmlspecialchars($product->title) ?></td>
                <td><?= htmlspecialchars($product->sku) ?></td>
                <td><?= htmlspecialchars($product->brand) ?></td>
                <td><?= htmlspecialchars($product->category) ?></td>
                <td><?= htmlspecialchars($product->shortDescription) ?></td>
                <td>$<?= number_format($product->price, 2) ?></td>
                <td class="checkbox-cell">
                    <input
                            type="checkbox"
                            name="enabled[]"
                            value="<?= $index ?>"
                            <?= $product->enabled ? 'checked' : '' ?>
                            onchange="toggleEnabled(<?= $index ?>, this.checked)">
                </td>
                <td class="button-cell">
                    <form action="/admin/products/<?= htmlspecialchars($product->sku) ?>" method="get"
                          style="display:inline;">
                        <button type="submit">Edit</button>
                    </form>
                </td>
                <td class="button-cell">
                    <form action="/admin/products/delete" method="post" style="display:inline;"
                          onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($product->title) ?>?');">
                        <input type="hidden" name="delete_sku" value="<?= htmlspecialchars($product->sku) ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginator -->
        <div class="paginator">
            <button>&lt;&lt;</button>
            <button>&lt;</button>
            <span>1</span>
            <button>&gt;</button>
            <button>&gt;&gt;</button>
        </div>
    </div>
</div>
<script src="/src/Presentation/public/js/message.js"></script>
</body>
</html>
