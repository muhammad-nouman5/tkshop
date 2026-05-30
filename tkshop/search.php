<?php
require_once 'config/database.php';
include 'includes/functions.php';

$currentUser = getCurrentUser();
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = null;
$searchPerformed = false;

if (!empty($query)) {
    $searchPerformed = true;
    // SQL Injection vulnerable (intentional for learning)
    $results = $conn->query("SELECT * FROM products WHERE name LIKE '%$query%' OR description LIKE '%$query%' OR category LIKE '%$query%'");
} else {
    // Show all products when no search query
    $results = $conn->query("SELECT * FROM products ORDER BY id DESC");
}

include 'includes/header.php';
?>

<style>
    .search-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .search-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .search-header h1 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .search-header p {
        color: #666;
        font-size: 1rem;
    }
    
    .search-box {
        max-width: 600px;
        margin: 0 auto 2rem;
    }
    
    .search-form {
        display: flex;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem;
        border-radius: 50px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .search-form input {
        flex: 1;
        padding: 0.8rem 1rem;
        border: none;
        border-radius: 50px;
        font-size: 1rem;
        outline: none;
    }
    
    .search-form button {
        background: #667eea;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 50px;
        color: white;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .search-form button:hover {
        background: #5a67d8;
    }
    
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eee;
    }
    
    .results-count {
        color: #666;
        font-size: 0.9rem;
    }
    
    .results-count strong {
        color: #667eea;
        font-size: 1.2rem;
    }
    
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .product-card a {
        text-decoration: none;
        color: inherit;
    }
    
    .product-image {
        height: 200px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
    }
    
    .product-info {
        padding: 1.2rem;
    }
    
    .product-category {
        font-size: 0.7rem;
        color: #667eea;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }
    
    .product-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .product-desc {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .product-price {
        font-size: 1.3rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .no-results {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 12px;
    }
    
    .no-results h3 {
        font-size: 1.5rem;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .no-results p {
        color: #666;
        margin-bottom: 1.5rem;
    }
    
    .suggestions {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }
    
    .suggestions h4 {
        color: #333;
        margin-bottom: 1rem;
    }
    
    .suggestion-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .suggestion-tag {
        background: #f0f0f0;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        text-decoration: none;
        color: #667eea;
        font-size: 0.9rem;
        transition: background 0.3s;
    }
    
    .suggestion-tag:hover {
        background: #667eea;
        color: white;
    }
    
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }
        
        .search-form input {
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
        }
        
        .search-form button {
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }
    }
</style>

<div class="search-container">
    <div class="search-header">
        <h1>🔍 Search Products</h1>
        <p>Find your favorite products from our collection</p>
    </div>
    
    <div class="search-box">
        <form class="search-form" method="GET" action="search.php">
            <input type="text" name="q" placeholder="Search by name, category or description..." value="<?= htmlspecialchars($query) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <div class="results-header">
        <h2><?= $searchPerformed ? "Search Results" : "All Products" ?></h2>
        <?php if ($results && $results->num_rows > 0): ?>
            <div class="results-count">
                Found <strong><?= $results->num_rows ?></strong> product<?= $results->num_rows > 1 ? 's' : '' ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($conn->error): ?>
        <div class="alert alert-danger">
            <strong>SQL Debug:</strong> <?= $conn->error ?>
        </div>
    <?php endif; ?>
    
    <?php if ($results && $results->num_rows > 0): ?>
        <div class="products-grid">
            <?php 
            $emojis = ['📱', '💻', '⌨️', '🖱️', '🎧', '🔊', '📷', '🎮', '⌚', '🔌'];
            $i = 0;
            while ($product = $results->fetch_assoc()): 
            ?>
                <div class="product-card">
                    <a href="product.php?id=<?= $product['id'] ?>">
                        <div class="product-image">
                            <?= $emojis[$i % count($emojis)] ?>
                        </div>
                        <div class="product-info">
                            <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-desc"><?= htmlspecialchars(substr($product['description'], 0, 80)) ?>...</div>
                            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                        </div>
                    </a>
                </div>
            <?php $i++; endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <h3>😕 No products found</h3>
            <p>We couldn't find any products matching "<?= htmlspecialchars($query) ?>"</p>
            <a href="search.php" class="btn-nav btn-filled">View All Products</a>
            
            <div class="suggestions">
                <h4>Suggestions:</h4>
                <div class="suggestion-tags">
                    <a href="search.php?q=laptop" class="suggestion-tag">laptop</a>
                    <a href="search.php?q=mouse" class="suggestion-tag">mouse</a>
                    <a href="search.php?q=keyboard" class="suggestion-tag">keyboard</a>
                    <a href="search.php?q=headphones" class="suggestion-tag">headphones</a>
                    <a href="search.php?q=monitor" class="suggestion-tag">monitor</a>
                    <a href="search.php?q=gaming" class="suggestion-tag">gaming</a>
                    <a href="search.php?q=wireless" class="suggestion-tag">wireless</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>