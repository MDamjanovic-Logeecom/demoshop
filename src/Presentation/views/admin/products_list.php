<?php /** @var array $products */ ?>
<div class="main-box">
    <h2>Products</h2>

    <div class="box-buttons">
        <div class="left-buttons">
            <button type="button" onclick="window.location.href='/admin/products/create'">Add new product</button>
            <button>Delete selected</button>
            <button>Enable selected</button>
        </div>
        <div class="right-buttons">
            <button>Filter</button>
        </div>
    </div>

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
            <tr>
                <td><input type="checkbox" value="<?= $index ?>"></td>
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
                    <button type="button"
                            style="display:inline;"
                            onclick="deleteProduct('<?= htmlspecialchars($product->sku) ?>', '<?= htmlspecialchars($product->title) ?>')">
                        Delete
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

