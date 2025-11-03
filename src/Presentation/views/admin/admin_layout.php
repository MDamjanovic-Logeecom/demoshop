
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Demo Shop - Admin</title>
    <link rel="stylesheet" href="/src/Presentation/public/css/products_list.css">
    <link rel="stylesheet" href="/src/Presentation/public/css/dashboard.css">
    <link rel="stylesheet" href="/src/Presentation/public/css/categories.css">
</head>
<body>
<div class="layout">
    <div class="sideMenu">
        <button class="side-btn active" data-target="dashboard">Dashboard</button>
        <button class="side-btn" data-target="products">Products</button>
        <button class="side-btn" data-target="categories">Categories</button>
    </div>
    <div class="main-box">
        <main id="content" class="content"></main>
    </div>
</div>
<script type="module" src="/src/Presentation/public/js/AJAX/main.js"></script>
<script src="/src/Presentation/public/js/message.js"></script>
</body>
</html>

