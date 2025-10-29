<?php
/** @var array $dashboardData */
$dashboardData = $dashboardData ?? [
        'productsCount' => 42,
        'categoriesCount' => 7,
        'homeVisits' => 123,
        'mostViewedProduct' => 'Super Widget',
        'productViews' => 56
];
?>

<div class="main-box">
    <h2>Dashboard</h2>

    <div class="dashboard-layout">
        <div class="dashboard-left">
            <div class="dashboard-row">
                <span class="label">Products count:</span>
                <span class="value"><?= $dashboardData['productsCount'] ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">Categories count:</span>
                <span class="value"><?= $dashboardData['categoriesCount'] ?></span>
            </div>
        </div>

        <div class="dashboard-right">
            <div class="dashboard-row">
                <span class="label">Home page opening count:</span>
                <span class="value"><?= $dashboardData['homeVisits'] ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">The most often viewed product:</span>
                <span class="value"><?= htmlspecialchars($dashboardData['mostViewedProduct']) ?></span>
            </div>
            <div class="dashboard-row">
                <span class="label">Number of product views:</span>
                <span class="value"><?= $dashboardData['productViews'] ?></span>
            </div>
        </div>
    </div>
</div>

