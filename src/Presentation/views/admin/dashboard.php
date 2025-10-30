<?php
/**
 * @var int $productCount
 * @var int $categoriesCount
 * @var int $homePageViews
 * @var string $mostViewedProduct
 * @var int $productViews
 */
?>

<div class="main-box">
    <h2>Dashboard</h2>
    <div class="dashboard-layout">
        <div class="dashboard-left">
            <div class="dashboard-row">
                <span class="label">Products count:</span>
                <span class="value"><?= htmlspecialchars($productCount) ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">Categories count:</span>
                <span class="value"><?= htmlspecialchars($categoriesCount) ?></span>
            </div>
        </div>

        <div class="dashboard-right">
            <div class="dashboard-row">
                <span class="label">Home page opening count:</span>
                <span class="value"><?= htmlspecialchars($homePageViews) ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">The most often viewed product:</span>
                <span class="value"><?= htmlspecialchars($mostViewedProduct) ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">Number of product views:</span>
                <span class="value"><?= htmlspecialchars($productViews) ?></span>
            </div>
        </div>
    </div>
</div>

