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

        /* Big container box */
        .main-box {
            position: relative;
            width: 90%;
            margin: 0 auto;
            background-color: #fff;
            border: 2px solid #ccc;
            padding: 20px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
        }

        /* Title inside the box */
        .main-box h2 {
            margin: 0 0 10px 0;
        }

        /* Top-left buttons outside the box */
        .side-buttons {
            position: absolute;
            top: -10px;
            left: -90px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .side-buttons button {
            padding: 8px 12px;
            cursor: pointer;
        }

        /* Buttons inside the box, left and right */
        .box-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .box-buttons .left-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .box-buttons .left-buttons button,
        .box-buttons .right-buttons button {
            padding: 6px 10px;
            cursor: pointer;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
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
    </style>
</head>
<body>

<div class="main-box">
    <!-- Top-left outside buttons -->
    <div class="side-buttons">
        <button>Button 1</button>
        <button>Button 2</button>
        <button>Button 3</button>
        <button>Button 4</button>
    </div>

    <!-- Box title -->
    <h2>Products</h2>

    <!-- Buttons inside the box -->
    <div class="box-buttons">
        <div class="left-buttons">
            <button>Left 1</button>
            <button>Left 2</button>
            <button>Left 3</button>
            <button>Left 4</button>
        </div>
        <div class="right-buttons">
            <button>Right</button>
        </div>
    </div>

    <!-- Main table -->
    <table>
        <thead>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
        </tr>
        </thead>
        <tbody>
        <?php for($i=1; $i<=10; $i++): ?>
            <tr>
                <td>Product <?= $i ?></td>
                <td>$<?= rand(10,100) ?></td>
                <td><?= rand(0,50) ?></td>
                <td>Category <?= rand(1,5) ?></td>
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

</body>
</html>

