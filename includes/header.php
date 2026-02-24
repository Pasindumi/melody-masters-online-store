<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters Instrument Shop</title>
    <!-- Use absolute path for CSS so it works in subdirectories -->
    <link rel="stylesheet" href="/melody-masters/css/style.css">
    <!-- Google Fonts for professional typography -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">
            <a href="/melody-masters/index.php">
                <span class="logo-icon">ðŸŽ¸</span>
                <h1>Melody<span>Masters</span></h1>
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="/melody-masters/index.php">Home</a></li>
                <li><a href="/melody-masters/shop.php">Shop</a></li>
                <li><a href="/melody-masters/cart.php" class="cart-link">
                    Cart <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                </a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/melody-masters/<?php echo strtolower($_SESSION['role']); ?>/dashboard.php">Dashboard</a></li>
                    <li><a href="/melody-masters/logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="/melody-masters/login.php">Login</a></li>
                    <li><a href="/melody-masters/register.php" class="btn-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="container">
