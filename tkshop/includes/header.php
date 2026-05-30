<?php
// Current page detection for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TKShop - Best Online Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Navbar Styles - Clean Design */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; overflow-x: hidden; }
        .navbar { background: #1a1a2e; position: sticky; top: 0; z-index: 1000; width: 100%; }
        .nav-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; height: 70px; }
        .logo a { font-size: 1.6rem; font-weight: bold; text-decoration: none; color: white; }
        .logo span { color: #667eea; }
        .nav-links { display: flex; align-items: center; gap: 0.5rem; list-style: none; }
        .nav-links li a { color: #e0e0e0; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500; font-size: 0.95rem; transition: all 0.3s; }
        .nav-links li a:hover { background: #667eea; color: white; }
        .nav-links li a.active { background: #667eea; color: white; }
        
        /* Cart Link - Extra Space on Right */
        .cart-link { 
            position: relative; 
            margin-right: 1.5rem;
        }
        
        .cart-badge { 
            position: absolute; 
            top: -6px; 
            right: -6px; 
            background: #ff4757; 
            color: white; 
            border-radius: 50%; 
            width: 18px; 
            height: 18px; 
            font-size: 0.65rem; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        
        /* Search Bar - With Margin on Both Sides */
        .search-bar { 
            display: flex; 
            align-items: center; 
            background: rgba(255,255,255,0.1); 
            border-radius: 30px; 
            padding: 0.2rem 0.3rem 0.2rem 1rem; 
            margin-left: 1rem;
            margin-right: 1rem;
        }
        
        .search-bar input { 
            background: none; 
            border: none; 
            padding: 0.4rem; 
            color: white; 
            outline: none; 
            width: 130px; 
            font-size: 0.8rem; 
        }
        
        .search-bar button { 
            background: #667eea; 
            border: none; 
            padding: 0.35rem 0.7rem; 
            border-radius: 30px; 
            color: white; 
            cursor: pointer; 
            font-size: 0.75rem; 
        }
        
        /* Nav Actions - Better Spacing */
        .nav-actions { 
            display: flex; 
            align-items: center; 
            gap: 0.8rem; 
        }
        
        .user-info { 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
            padding: 0.3rem 0.8rem; 
            background: rgba(255,255,255,0.1); 
            border-radius: 30px; 
            text-decoration: none; 
        }
        
        .user-avatar { 
            width: 32px; 
            height: 32px; 
            background: #667eea; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            color: white; 
        }
        
        .user-name { 
            color: white; 
            font-size: 0.85rem; 
        }
        
        .btn-nav { 
            padding: 0.45rem 1.1rem; 
            border-radius: 6px; 
            text-decoration: none; 
            font-weight: 500; 
            font-size: 0.85rem; 
        }
        
        .btn-outline { 
            border: 1px solid #667eea; 
            color: #667eea; 
            background: transparent; 
        }
        
        .btn-outline:hover { 
            background: #667eea; 
            color: white; 
        }
        
        .btn-filled { 
            background: #667eea; 
            color: white; 
        }
        
        .hamburger { 
            display: none; 
            flex-direction: column; 
            cursor: pointer; 
            background: none; 
            border: none; 
            padding: 0.5rem; 
        }
        
        .hamburger span { 
            width: 24px; 
            height: 2px; 
            background: white; 
            margin: 3px 0; 
            border-radius: 2px; 
        }
        
        .mobile-nav { 
            position: fixed; 
            top: 0; 
            right: -100%; 
            width: 75%; 
            max-width: 300px; 
            height: 100%; 
            background: #1a1a2e; 
            z-index: 1000; 
            transition: right 0.3s; 
            padding: 70px 1.5rem 2rem; 
        }
        
        .mobile-nav.active { 
            right: 0; 
        }
        
        .mobile-nav .mobile-links li { 
            list-style: none; 
            margin-bottom: 0.5rem; 
        }
        
        .mobile-nav .mobile-links li a { 
            display: block; 
            padding: 0.7rem 0; 
            color: white; 
            text-decoration: none; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        
        .mobile-nav .mobile-links li a.active { 
            color: #667eea; 
            font-weight: bold;
        }
        
        .mobile-close { 
            position: absolute; 
            top: 20px; 
            right: 20px; 
            background: none; 
            border: none; 
            color: white; 
            font-size: 1.3rem; 
            cursor: pointer; 
        }
        
        .mobile-cart-badge { 
            background: #ff4757; 
            padding: 2px 8px; 
            border-radius: 20px; 
            font-size: 0.7rem; 
            margin-left: 8px; 
        }
        
        @media (max-width: 900px) { 
            .nav-links { display: none; } 
            .hamburger { display: flex; } 
            .search-bar { display: none; } 
            .user-name { display: none; } 
        }
        
        @media (min-width: 901px) { 
            .hamburger { display: none; } 
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="index.php">TK<span>Shop</span></a></div>
        
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="products.php" class="<?= ($current_page == 'products.php' || $current_page == 'product.php') ? 'active' : '' ?>">Products</a></li>
            <li><a href="search.php" class="<?= $current_page == 'search.php' ? 'active' : '' ?>">Search</a></li>
            <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
            <li class="cart-link"><a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart</a><span class="cart-badge" id="cartCount">0</span></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li><a href="admin/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'admin') !== false ? 'active' : '' ?>">Admin</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="search-bar">
            <form action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search">
                <button type="submit">Go</button>
            </form>
        </div>
        
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
                    <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                </a>
                <a href="logout.php" class="btn-nav btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-nav btn-outline">Login</a>
                <a href="register.php" class="btn-nav btn-filled">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <button class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></button>
    </div>
</nav>

<div class="mobile-nav" id="mobileNav">
    <button class="mobile-close" id="mobileCloseBtn">✕</button>
    <ul class="mobile-links">
        <li><a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a></li>
        <li><a href="products.php" class="<?= ($current_page == 'products.php' || $current_page == 'product.php') ? 'active' : '' ?>">Products</a></li>
        <li><a href="search.php" class="<?= $current_page == 'search.php' ? 'active' : '' ?>">Search</a></li>
        <li><a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
        <li><a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart <span class="mobile-cart-badge" id="mobileCartCount">0</span></a></li>
        <li><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>">My Profile</a></li>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <li><a href="admin/index.php" class="<?= strpos($_SERVER['REQUEST_URI'], 'admin') !== false ? 'active' : '' ?>">Admin Panel</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</div>

<script>
    // Mobile menu toggle
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileCloseBtn = document.getElementById('mobileCloseBtn');
    
    function openMobileMenu() { mobileNav.classList.add('active'); hamburgerBtn.classList.add('active'); document.body.style.overflow = 'hidden'; }
    function closeMobileMenu() { mobileNav.classList.remove('active'); hamburgerBtn.classList.remove('active'); document.body.style.overflow = ''; }
    
    hamburgerBtn.addEventListener('click', openMobileMenu);
    mobileCloseBtn.addEventListener('click', closeMobileMenu);
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900 && mobileNav.classList.contains('active')) closeMobileMenu();
    });
    
    // Function to update cart count from session
    function updateCartCount() {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cartCount');
                const mobileBadge = document.getElementById('mobileCartCount');
                if (cartBadge) cartBadge.innerText = data.count;
                if (mobileBadge) mobileBadge.innerText = data.count;
            })
            .catch(error => console.log('Error fetching cart count:', error));
    }
    
    // Update cart count on every page load
    updateCartCount();
</script>

<main>