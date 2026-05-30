<?php
require_once 'config/database.php';
include 'includes/functions.php';

$currentUser = getCurrentUser();
$error = null;
$product = null;

// SQL Injection vulnerable but with error handling
$id = isset($_GET['id']) ? $_GET['id'] : '1';

// Execute query with error suppression for better error message
$result = @$conn->query("SELECT * FROM products WHERE id = $id");

if ($conn->error) {
    $error = "Invalid product ID or SQL syntax error!";
} elseif ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    $error = "Product not found!";
}

// Handle review submission
$review_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    if (!$currentUser) {
        $review_msg = '<div class="alert alert-warning">Please login to leave a review.</div>';
    } else {
        $user_id = $currentUser['id'];
        $comment = $_POST['comment'];
        $rating = (int)$_POST['rating'];
        
        // Vulnerable to SQL Injection (intentional)
        $conn->query("INSERT INTO reviews (user_id, product_id, comment, rating) VALUES ($user_id, $id, '$comment', $rating)");
        $review_msg = '<div class="alert alert-success">Review added successfully!</div>';
    }
}

// Get reviews
$reviews = $conn->query("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE product_id = $id ORDER BY r.created_at DESC");

include 'includes/header.php';
?>

<style>
    /* Product Page Styles */
    .product-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        margin-bottom: 2rem;
        font-size: 0.85rem;
        color: #666;
    }
    
    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    /* Error Alert */
    .alert {
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .alert-danger {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }
    
    .alert-warning {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #d97706;
    }
    
    .alert-success {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #059669;
    }
    
    /* Product Detail Grid */
    .product-detail {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    
    /* Product Image */
    .product-image-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
    }
    
    .product-emoji {
        font-size: 12rem;
        filter: drop-shadow(0 10px 20px rgba(0,0,0,0.2));
    }
    
    /* Product Info */
    .product-info-section h1 {
        font-size: 2rem;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    
    .product-category {
        display: inline-block;
        background: #e0e7ff;
        color: #667eea;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .product-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin: 1rem 0;
    }
    
    .product-description {
        color: #4b5563;
        line-height: 1.6;
        margin: 1rem 0;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
    }
    
    /* Stock Status */
    .stock-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
        padding: 0.75rem;
        background: #f3f4f6;
        border-radius: 12px;
    }
    
    .in-stock {
        color: #10b981;
        font-weight: 600;
    }
    
    .out-of-stock {
        color: #ef4444;
        font-weight: 600;
    }
    
    /* Add to Cart Form */
    .cart-form {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: 1.5rem;
    }
    
    .quantity-input {
        width: 80px;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
        font-size: 1rem;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary:hover {
        background: #5a67d8;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #4b5563;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    
    /* Product Meta */
    .product-meta {
        display: flex;
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding-top: 1rem;
        font-size: 0.85rem;
        color: #6b7280;
    }
    
    /* Reviews Section */
    .reviews-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .reviews-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f3f4f6;
    }
    
    .reviews-header h3 {
        font-size: 1.3rem;
        color: #1a1a2e;
    }
    
    /* Review Form */
    .review-form {
        background: #f9fafb;
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
    }
    
    .review-form h4 {
        margin-bottom: 1rem;
        color: #1a1a2e;
    }
    
    .review-form textarea {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        resize: vertical;
        font-family: inherit;
        margin-bottom: 1rem;
    }
    
    .review-form select {
        padding: 0.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        margin-right: 1rem;
    }
    
    .review-form button {
        background: #667eea;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
    }
    
    /* Review List */
    .review-list {
        max-height: 500px;
        overflow-y: auto;
    }
    
    .review-item {
        padding: 1.25rem;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .review-item:last-child {
        border-bottom: none;
    }
    
    .review-author {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .review-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
    
    .review-info h4 {
        font-size: 0.95rem;
        color: #1a1a2e;
    }
    
    .review-rating {
        color: #fbbf24;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    .review-comment {
        color: #4b5563;
        line-height: 1.5;
        margin-top: 0.5rem;
    }
    
    .review-date {
        font-size: 0.7rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
    
    /* SQL Error Message */
    .sql-error {
        background: #1e1e2e;
        color: #f87171;
        padding: 1rem;
        border-radius: 12px;
        font-family: monospace;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #ef4444;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .product-detail {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
        }
        
        .product-emoji {
            font-size: 8rem;
        }
        
        .product-image-section {
            min-height: 250px;
        }
        
        .product-info-section h1 {
            font-size: 1.5rem;
        }
        
        .product-price {
            font-size: 2rem;
        }
    }
</style>

<div class="product-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Home</a> / 
        <a href="products.php">Products</a> / 
        <span>Product Details</span>
    </div>
    
    <?php if ($error): ?>
        <!-- SQL Error Message - Shows when SQL injection is attempted -->
        <div class="sql-error">
            <strong>⚠️ Database Error:</strong><br>
            <?= htmlspecialchars($error) ?><br><br>
            <small>Query attempted: SELECT * FROM products WHERE id = <?= htmlspecialchars($id) ?></small>
        </div>
        <div class="alert alert-danger">
            Product not found or invalid product ID!
        </div>
    <?php elseif ($product): ?>
    
    <!-- Product Detail -->
    <div class="product-detail">
        <div class="product-image-section">
            <div class="product-emoji">
                <?php
                $emojis = ['📱', '💻', '⌨️', '🖱️', '🎧', '🔊', '📷', '🎮', '⌚', '🔌'];
                $index = ($product['id'] - 1) % count($emojis);
                echo $emojis[$index];
                ?>
            </div>
        </div>
        
        <div class="product-info-section">
            <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
            
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            
            <div class="stock-status">
                <?php if ($product['stock'] > 0): ?>
                    <span class="in-stock">✓ In Stock</span>
                    <span style="color: #6b7280;">(<?= $product['stock'] ?> units available)</span>
                <?php else: ?>
                    <span class="out-of-stock">✗ Out of Stock</span>
                <?php endif; ?>
            </div>
            
            <form class="cart-form" method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="quantity-input">
                <button type="submit" name="add_to_cart" class="btn-primary">🛒 Add to Cart</button>
            </form>
            
            <div class="product-meta">
                <span>📦 Free Shipping</span>
                <span>🔄 30-Day Returns</span>
                <span>🔒 Secure Checkout</span>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="reviews-section">
        <div class="reviews-header">
            <h3>📝 Customer Reviews</h3>
            <span class="review-count"><?= $reviews->num_rows ?> Review<?= $reviews->num_rows != 1 ? 's' : '' ?></span>
        </div>
        
        <!-- Review Form -->
        <div class="review-form">
            <h4>Write a Review</h4>
            <?= $review_msg ?>
            <?php if ($currentUser): ?>
                <form method="POST">
                    <textarea name="comment" rows="3" placeholder="Share your experience with this product..."></textarea>
                    <select name="rating">
                        <option value="5">★★★★★ (5/5) - Excellent</option>
                        <option value="4">★★★★☆ (4/5) - Good</option>
                        <option value="3">★★★☆☆ (3/5) - Average</option>
                        <option value="2">★★☆☆☆ (2/5) - Poor</option>
                        <option value="1">★☆☆☆☆ (1/5) - Terrible</option>
                    </select>
                    <button type="submit" name="review">Submit Review</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to leave a review.</p>
            <?php endif; ?>
        </div>
        
        <!-- Reviews List -->
        <div class="review-list">
            <?php if ($reviews && $reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-item">
                        <div class="review-author">
                            <div class="review-avatar">
                                <?= strtoupper(substr($review['username'], 0, 1)) ?>
                            </div>
                            <div class="review-info">
                                <h4><?= htmlspecialchars($review['username']) ?></h4>
                                <div class="review-rating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if($i <= $review['rating']): ?>
                                            ★
                                        <?php else: ?>
                                            ☆
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-comment">
                            <?= nl2br(htmlspecialchars($review['comment'])) ?>
                        </div>
                        <div class="review-date">
                            <?= date('F j, Y', strtotime($review['created_at'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 2rem; color: #6b7280;">
                    No reviews yet. Be the first to review this product!
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>