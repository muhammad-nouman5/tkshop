<?php
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    // SQL Injection vulnerable
    $result = $conn->query("SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: profile.php');
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}

include 'includes/header.php';
?>

<style>
    .login-container {
        max-width: 400px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .login-container h2 {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
    }
    
    .btn-login {
        width: 100%;
        background: #667eea;
        color: white;
        padding: 0.75rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="login-container">
    <h2>Login to TKShop</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-error">⚠️ <?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn-login">Login</button>
    </form>
    
    <p style="text-align:center; margin-top:1rem;">
        Don't have an account? <a href="register.php">Sign up</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>