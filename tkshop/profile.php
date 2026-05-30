<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUser = getCurrentUser();

// IDOR vulnerability - can view any user profile
$view_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];

// Get profile data
$profile = $conn->query("SELECT * FROM users WHERE id = $view_id")->fetch_assoc();

if (!$profile) {
    header('Location: index.php');
    exit;
}

$update_msg = '';

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $uid = (int)$_POST['user_id'];
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $full_name = $conn->real_escape_string($_POST['full_name'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    
    $conn->query("UPDATE users SET email='$email', full_name='$full_name', address='$address', phone='$phone' WHERE id=$uid");
    $update_msg = "Profile updated successfully!";
    $profile = $conn->query("SELECT * FROM users WHERE id = $view_id")->fetch_assoc();
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_pass = md5($_POST['old_password'] ?? '');
    $new_pass = md5($_POST['new_password'] ?? '');
    
    $check = $conn->query("SELECT id FROM users WHERE id={$currentUser['id']} AND password='$old_pass'");
    if ($check && $check->num_rows > 0) {
        $conn->query("UPDATE users SET password='$new_pass' WHERE id={$currentUser['id']}");
        $update_msg = "Password changed successfully!";
    } else {
        $update_msg = "Current password is incorrect!";
    }
}

// Upload profile image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_image'])) {
    if (!is_dir('uploads/profiles')) {
        mkdir('uploads/profiles', 0777, true);
    }
    
    if ($_FILES['new_image']['error'] == 0) {
        $filename = time() . '_' . basename($_FILES['new_image']['name']);
        $target = 'uploads/profiles/' . $filename;
        
        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target)) {
            $conn->query("UPDATE users SET profile_image = '$target' WHERE id = {$currentUser['id']}");
            $update_msg = "Profile picture updated!";
            $profile = $conn->query("SELECT * FROM users WHERE id = $view_id")->fetch_assoc();
        }
    }
}

// Get user orders
$orders = $conn->query("SELECT o.*, p.name as product_name FROM orders o JOIN products p ON o.product_id = p.id WHERE o.user_id = $view_id ORDER BY o.created_at DESC LIMIT 10");

include 'includes/header.php';
?>

<style>
    .profile-container { max-width: 1200px; margin: 0 auto; padding: 2rem 1rem; }
    .profile-header { background: white; border-radius: 24px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; }
    .profile-avatar-large { position: relative; }
    .profile-avatar-large img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; }
    .default-avatar { width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: white; }
    .upload-btn { position: absolute; bottom: 5px; right: 5px; background: #667eea; border: none; color: white; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; font-size: 1rem; }
    .profile-info h1 { font-size: 1.8rem; color: #1a1a2e; margin-bottom: 0.5rem; }
    .profile-meta { display: flex; gap: 1rem; flex-wrap: wrap; margin: 0.5rem 0; }
    .badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-admin { background: #fee2e2; color: #dc2626; }
    .badge-user { background: #d1fae5; color: #065f46; }
    .profile-balance { margin-left: auto; text-align: center; padding: 1rem 1.5rem; background: #f1f5f9; border-radius: 20px; }
    .balance-amount { font-size: 1.8rem; font-weight: bold; color: #10b981; }
    .balance-label { font-size: 0.8rem; color: #64748b; }
    .profile-card { background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .profile-card h3 { margin-bottom: 1rem; color: #1a1a2e; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 500; color: #333; }
    .form-control { width: 100%; padding: 0.7rem; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 0.95rem; }
    .form-control:focus { outline: none; border-color: #667eea; }
    .btn-primary { background: #667eea; color: white; border: none; padding: 0.7rem 1.5rem; border-radius: 12px; font-weight: 600; cursor: pointer; }
    .alert-success { background: #d1fae5; color: #065f46; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem; }
    .alert-warning { background: #fef3c7; color: #92400e; padding: 0.75rem; border-radius: 12px; margin-bottom: 1rem; }
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th, .orders-table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .orders-table th { background: #f8fafc; font-weight: 600; }
    @media (max-width: 768px) { .profile-header { flex-direction: column; text-align: center; } .profile-balance { margin-left: 0; } }
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar-large">
            <?php if (!empty($profile['profile_image']) && file_exists($profile['profile_image'])): ?>
                <img src="<?= $profile['profile_image'] ?>" alt="Profile Picture">
            <?php else: ?>
                <div class="default-avatar"><?= strtoupper(substr($profile['username'], 0, 1)) ?></div>
            <?php endif; ?>
            
            <?php if ($view_id == $currentUser['id']): ?>
                <form method="POST" enctype="multipart/form-data">
                    <button type="button" class="upload-btn" onclick="document.getElementById('imageUpload').click()">📷</button>
                    <input type="file" name="new_image" id="imageUpload" style="display:none" onchange="this.form.submit()">
                </form>
            <?php endif; ?>
        </div>
        
        <div class="profile-info">
            <h1><?= htmlspecialchars($profile['full_name'] ?: $profile['username']) ?></h1>
            <div class="profile-meta">
                <span class="badge <?= $profile['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= $profile['role'] ?></span>
                <span>📧 <?= htmlspecialchars($profile['email']) ?></span>
                <span>📅 Joined <?= date('M Y', strtotime($profile['created_at'])) ?></span>
            </div>
            <?php if ($profile['phone']): ?><p>📞 <?= htmlspecialchars($profile['phone']) ?></p><?php endif; ?>
            <?php if ($profile['address']): ?><p>📍 <?= htmlspecialchars($profile['address']) ?></p><?php endif; ?>
        </div>
        
        <div class="profile-balance">
            <div class="balance-amount">$<?= number_format($profile['balance'], 2) ?></div>
            <div class="balance-label">Account Balance</div>
        </div>
    </div>
    
    <?php if ($update_msg): ?><div class="alert-success">✅ <?= $update_msg ?></div><?php endif; ?>
    
    <?php if ($view_id != $currentUser['id']): ?>
        <div class="alert-warning">⚠️ You are viewing another user's profile! (IDOR Vulnerability)</div>
    <?php endif; ?>
    
    <?php if ($view_id == $currentUser['id']): ?>
    <div class="profile-card">
        <h3>✏️ Edit Profile</h3>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $view_id ?>">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($profile['email']) ?>" required></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"></div>
            <div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($profile['address'] ?? '') ?></textarea></div>
            <button type="submit" name="update_profile" class="btn-primary">Save Changes</button>
        </form>
    </div>
    
    <div class="profile-card">
        <h3>🔑 Change Password</h3>
        <form method="POST">
            <div class="form-group"><label>Current Password</label><input type="password" name="old_password" class="form-control"></div>
            <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control"></div>
            <button type="submit" name="change_password" class="btn-primary">Update Password</button>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="profile-card">
        <h3>🛒 Recent Orders</h3>
        <?php if ($orders && $orders->num_rows > 0): ?>
            <table class="orders-table"><thead><tr><th>Product</th><th>Qty</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody><?php while ($order = $orders->fetch_assoc()): ?>
            <tr><td><?= htmlspecialchars($order['product_name']) ?></td><td><?= $order['quantity'] ?></td><td>$<?= number_format($order['total'], 2) ?></td><td><?= $order['status'] ?></td><td><?= date('M j, Y', strtotime($order['created_at'])) ?></td></tr>
            <?php endwhile; ?></tbody></table>
        <?php else: ?>
            <p>No orders yet. <a href="products.php">Start shopping!</a></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>