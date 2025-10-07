<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demo Shop - Main Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        /* --- Page layout wrapper --- */
        .layout {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 0px;
            flex-wrap: wrap;
        }

        /* Left-side buttons column */
        .side-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .side-buttons button {
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px 0 0 4px;
            border-right: none;
            text-align: left;
        }

        /* Big box */
        .main-box {
            flex: 1;
            min-width: 300px;
            max-width: 1200px;
            background-color: #fff;
            border: 2px solid #ccc;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        .main-box h2 {
            margin: 0 0 15px 0;
        }

        /* Inside buttons */
        .box-buttons {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .box-buttons .left-buttons {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;
        }

        .box-buttons button {
            padding: 2px 10px;
            cursor: pointer;
            background-color: white;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Paginator */
        .paginator {
            text-align: center;
        }

        .paginator button {
            padding: 5px 10px;
            margin: 0 2px;
            cursor: pointer;
        }

        /* Center content in table cells */
        td.checkbox-cell,
        td.button-cell {
            text-align: center;
            vertical-align: middle;
        }

        /* Optional: make buttons a bit nicer */
        td.button-cell button {
            padding: 4px 8px;
            cursor: pointer;
            background-color: white;
            border-color: #cccccc;
        }

    </style>
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
                <button>Add new product</button>
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
                <th>     </th>
                <th>     </th>
            </tr>
            </thead>
            <tbody>
            <?php for($i=1; $i<=10; $i++): ?>
                <tr>
                    <td class="checkbox-cell">
                        <input type="checkbox" name="select_item[]" value="<?= $i ?>">
                    </td>
                    <td>Title <?= $i ?></td>
                    <td>$<?= rand(10,100) ?></td>
                    <td>Brand <?= rand(1,5) ?></td>
                    <td>Category <?= rand(1,9) ?></td>
                    <td>Description <?= rand(1, 20) ?></td>
                    <td><?= rand(0,50) ?></td>
                    <td class="checkbox-cell">
                        <input type="checkbox" name="select_item[]" value="<?= $i ?>">
                    </td>
                    <td class="button-cell">
                        <button type="button" onclick="alert('Edit<?= $i ?>')">Edit</button>
                    </td>
                    <td class="button-cell">
                        <button type="button" onclick="alert('Delete<?= $i ?>')">Delete</button>
                    </td>
                </tr>
            <?php endfor; ?>
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

</body>
</html>
