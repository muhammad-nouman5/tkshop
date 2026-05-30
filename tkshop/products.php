<?php
require_once 'config/database.php';
include 'includes/functions.php';

$currentUser = getCurrentUser();

// SQL Injection in ORDER BY
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$where = $category ? "WHERE category = '$category'" : "";
$products = $conn->query("SELECT * FROM products $where ORDER BY $sort");

include 'includes/header.php';
?>

<div class="container">
    <div class="sidebar">
        <h3>Categories</h3>
        <a href="products.php">All</a>
        <a href="products.php?category=Electronics">Electronics</a>
        <a href="products.php?category=Accessories">Accessories</a>
        <a href="products.php?category=Audio">Audio</a>
        <a href="products.php?category=Wearables">Wearables</a>
        
        <h3>Sort By</h3>
        <a href="products.php?sort=id">Default</a>
        <a href="products.php?sort=price">Price: Low to High</a>
        <a href="products.php?sort=price DESC">Price: High to Low</a>
        <a href="products.php?sort=name">Name</a>
    </div>
    
    <div class="products-grid">
        <?php while ($product = $products->fetch_assoc()): ?>
            <div class="product-card">
                <a href="product.php?id=<?= $product['id'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <p class="price">$<?= $product['price'] ?></p>
                    <p><?= substr($product['description'], 0, 80) ?>...</p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>