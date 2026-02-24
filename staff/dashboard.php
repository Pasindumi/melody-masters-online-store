<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Staff');

// Get statistics
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5 AND is_digital = FALSE")->fetchColumn();

include '../includes/header.php';
?>

<div class="card">
    <h2>Staff Dashboard</h2>
    <p>Inventory and Product Management</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
        <div class="card" style="background: #e1f5fe; border: none;">
            <h3><?php echo $total_products; ?></h3>
            <p>Total Products</p>
        </div>
        <div class="card" style="background: <?php echo $low_stock > 0 ? '#ffebee' : '#e8f5e9'; ?>; border: none;">
            <h3><?php echo $low_stock; ?></h3>
            <p>Low Stock Alerts</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
        <a href="manage_inventory.php" class="btn btn-primary" style="text-align: center; padding: 2rem;">Manage Inventory</a>
        <a href="../index.php" class="btn btn-primary" style="text-align: center; padding: 2rem;">View Site</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
