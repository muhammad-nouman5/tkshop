<?php
require_once 'config/database.php';
include 'includes/functions.php';

$currentUser = getCurrentUser();

// Get featured products - SQL Injection possible
$category = isset($_GET['cat']) ? $_GET['cat'] : '';
if ($category) {
    $products = $conn->query("SELECT * FROM products WHERE category = '$category' LIMIT 6");
} else {
    $products = $conn->query("SELECT * FROM products LIMIT 6");
}

include 'includes/header.php';
?>

<div class="hero">
    <h1>Welcome to TKShop</h1>
    <p>Your one-stop shop for electronics and accessories</p>
    <a href="products.php" class="btn">Shop Now</a>
</div>

<div class="container">
    <h2>Featured Products</h2>
    <div class="products-grid">
        <?php while ($product = $products->fetch_assoc()): ?>
            <div class="product-card">
                <a href="product.php?id=<?= $product['id'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <p class="price">$<?= $product['price'] ?></p>
                    <p><?= substr($product['description'], 0, 100) ?>...</p>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>