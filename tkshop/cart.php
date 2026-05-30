<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

// Initialize cart in session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    header('Location: cart.php');
    exit;
}

// Update quantity
if (isset($_GET['update']) && isset($_GET['qty'])) {
    $id = (int)$_GET['update'];
    $qty = (int)$_GET['qty'];
    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty;
    } else {
        unset($_SESSION['cart'][$id]);
    }
    header('Location: cart.php');
    exit;
}

// Remove from cart
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header('Location: cart.php');
    exit;
}

// Get cart items
$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
    if ($product) {
        $subtotal = $product['price'] * $quantity;
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'stock' => $product['stock']
        ];
        $total += $subtotal;
    }
}

include 'includes/header.php';
?>

<style>
    .cart-container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
    .cart-header { text-align: center; margin-bottom: 2rem; }
    .cart-header h1 { font-size: 2rem; color: #1a1a2e; margin-bottom: 0.5rem; }
    .cart-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .cart-table { width: 100%; border-collapse: collapse; }
    .cart-table th { background: #f8fafc; padding: 1rem; text-align: left; font-weight: 600; color: #1e293b; border-bottom: 2px solid #e2e8f0; }
    .cart-table td { padding: 1rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
    .product-name { font-weight: 600; color: #1e293b; }
    .quantity-input { width: 60px; padding: 0.4rem; text-align: center; border: 2px solid #e2e8f0; border-radius: 8px; }
    .btn-remove { background: #ef4444; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; }
    .btn-update { background: #667eea; color: white; border: none; padding: 0.4rem 0.8rem; border-radius: 8px; cursor: pointer; font-size: 0.8rem; }
    .cart-total { text-align: right; padding: 1.5rem; background: #f8fafc; font-size: 1.2rem; font-weight: bold; }
    .cart-total span { color: #667eea; font-size: 1.5rem; }
    .empty-cart { text-align: center; padding: 3rem; color: #64748b; }
    .btn-checkout { background: #10b981; color: white; padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none; display: inline-block; margin-top: 1rem; font-weight: 600; }
    .btn-continue { background: #667eea; color: white; padding: 0.8rem 2rem; border-radius: 12px; text-decoration: none; display: inline-block; margin-top: 1rem; margin-right: 1rem; }
    @media (max-width: 768px) { .cart-table th, .cart-table td { padding: 0.5rem; font-size: 0.85rem; } .quantity-input { width: 50px; } }
</style>

<div class="cart-container">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
        <p>Review and manage your items</p>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <div class="cart-card">
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="products.php" class="btn-continue">Continue Shopping</a>
            </div>
        </div>
    <?php else: ?>
        <div class="cart-card">
            <table class="cart-table">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="product-name"><?= htmlspecialchars($item['name']) ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <form method="GET" style="display: flex; gap: 0.3rem; align-items: center;">
                                <input type="hidden" name="update" value="<?= $item['id'] ?>">
                                <input type="number" name="qty" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="quantity-input">
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                        <td>$<?= number_format($item['subtotal'], 2) ?></td>
                        <td><a href="?remove=<?= $item['id'] ?>" class="btn-remove" onclick="return confirm('Remove item?')">Remove</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-total">
                Total: $<?= number_format($total, 2) ?>
            </div>
        </div>
        <div style="text-align: right; margin-top: 1rem;">
            <a href="products.php" class="btn-continue">Continue Shopping</a>
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    function updateCartStorage() {
        let items = {};
        document.querySelectorAll('.cart-item').forEach(item => {
            let id = item.dataset.id;
            let qty = parseInt(item.querySelector('.qty-input')?.value || 1);
            if (qty > 0) items[id] = qty;
        });
        localStorage.setItem('cart', JSON.stringify(items));
        
        // Update badge
        let count = Object.values(items).reduce((a,b) => a+b, 0);
        document.getElementById('cartCount').innerText = count;
        let mobileBadge = document.getElementById('mobileCartBadge');
        if (mobileBadge) mobileBadge.innerText = count;
    }
</script>