<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$success = '';
$error = '';

// Get cart items from session
$cart_items = [];
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
        if ($product) {
            $subtotal = $product['price'] * $quantity;
            $cart_items[] = [
                'id' => $product_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
            $total += $subtotal;
        }
    }
}

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (empty($cart_items)) {
        $error = "Your cart is empty!";
    } elseif ($user['balance'] < $total) {
        $error = "Insufficient balance! Your balance: $" . number_format($user['balance'], 2);
    } else {
        // Place order for each item
        foreach ($cart_items as $item) {
            $product_id = $item['id'];
            $quantity = $item['quantity'];
            $subtotal = $item['subtotal'];
            
            // Insert order
            $conn->query("INSERT INTO orders (user_id, product_id, quantity, total) VALUES ({$user['id']}, $product_id, $quantity, $subtotal)");
            
            // Update user balance (vulnerable to negative quantity)
            $conn->query("UPDATE users SET balance = balance - $subtotal WHERE id = {$user['id']}");
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        $success = "Order placed successfully!";
        
        // Refresh user data
        $user = getCurrentUser();
        $total = 0;
        $cart_items = [];
    }
}

include 'includes/header.php';
?>

<style>
    .checkout-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .checkout-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .checkout-header h1 {
        font-size: 2rem;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }
    
    .checkout-header p {
        color: #64748b;
        font-size: 0.9rem;
    }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
    }
    
    /* Order Items Card */
    .order-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    
    .order-card h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .order-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .item-name {
        font-weight: 500;
        color: #1e293b;
    }
    
    .item-details {
        color: #64748b;
        font-size: 0.85rem;
    }
    
    .item-price {
        font-weight: 600;
        color: #1e293b;
    }
    
    /* Summary Card */
    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: sticky;
        top: 90px;
    }
    
    .summary-card h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
    }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        margin-top: 0.5rem;
        border-top: 2px solid #e2e8f0;
        font-weight: 700;
        font-size: 1.2rem;
        color: #1a1a2e;
    }
    
    .balance-info {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 1rem;
        margin: 1rem 0;
        text-align: center;
    }
    
    .balance-label {
        font-size: 0.8rem;
        color: #64748b;
    }
    
    .balance-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #10b981;
    }
    
    .btn-place-order {
        width: 100%;
        background: #667eea;
        color: white;
        border: none;
        padding: 1rem;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 0.5rem;
    }
    
    .btn-place-order:hover {
        background: #5a67d8;
        transform: translateY(-1px);
    }
    
    .btn-place-order:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
    }
    
    .empty-cart {
        text-align: center;
        padding: 2rem;
        color: #64748b;
    }
    
    .empty-cart a {
        color: #667eea;
        text-decoration: none;
    }
    
    .alert {
        padding: 0.85rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .back-link {
        display: inline-block;
        margin-top: 1rem;
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
        
        .summary-card {
            position: static;
        }
        
        .checkout-header h1 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Review your order and complete purchase</p>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            ✅ <?= $success ?>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="products.php" class="back-link">← Continue Shopping</a>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-error">
            ⚠️ <?= $error ?>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
            <a href="cart.php" class="back-link">← Back to Cart</a>
        </div>
    <?php elseif (empty($cart_items)): ?>
        <div class="order-card">
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="products.php">Browse Products</a>
            </div>
        </div>
    <?php else: ?>
    
    <div class="checkout-grid">
        <!-- Left Side - Order Items -->
        <div class="order-card">
            <h3>Order Items</h3>
            
            <?php foreach ($cart_items as $item): ?>
            <div class="order-item">
                <div>
                    <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="item-details">Quantity: <?= $item['quantity'] ?></div>
                </div>
                <div class="item-price">$<?= number_format($item['subtotal'], 2) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Right Side - Payment Summary -->
        <div class="summary-card">
            <h3>Payment Summary</h3>
            
            <div class="summary-row">
                <span>Subtotal</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>$<?= number_format($total, 2) ?></span>
            </div>
            
            <div class="balance-info">
                <div class="balance-label">Your Balance</div>
                <div class="balance-amount">$<?= number_format($user['balance'], 2) ?></div>
            </div>
            
            <form method="POST">
                <button type="submit" name="place_order" class="btn-place-order">
                    Place Order
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="cart.php" class="back-link">← Back to Cart</a>
            </div>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>