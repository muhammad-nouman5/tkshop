<?php
require_once '../config/database.php';
include '../includes/functions.php';

// No admin check - broken access control
$users = $conn->query("SELECT * FROM users");
$orders = $conn->query("SELECT * FROM orders");
$products = $conn->query("SELECT * FROM products");

include '../includes/header.php';
?>

<div class="container">
    <h2>Admin Dashboard</h2>
    
    <div class="stats">
        <div class="stat">
            <h3>Total Users</h3>
            <p><?= $users->num_rows ?></p>
        </div>
        <div class="stat">
            <h3>Total Orders</h3>
            <p><?= $orders->num_rows ?></p>
        </div>
        <div class="stat">
            <h3>Total Products</h3>
            <p><?= $products->num_rows ?></p>
        </div>
    </div>
    
    <h3>All Users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Balance</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['email'] ?></td>
            <td><?= $user['role'] ?></td>
            <td>$<?= $user['balance'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>