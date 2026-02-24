<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Customer');

include '../includes/header.php';
?>

<div class="card">
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Your Customer Dashboard</p>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
        <a href="ordeRs. php" class="btn btn-primary" style="text-align: center; padding: 2rem;">My Orders</a>
        <a href="downloads.php" class="btn btn-primary" style="text-align: center; padding: 2rem;">My Downloads</a>
        <a href="../shop.php" class="btn btn-primary" style="text-align: center; padding: 2rem;">Back to Shop</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
