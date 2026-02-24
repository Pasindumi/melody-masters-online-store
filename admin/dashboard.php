<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Admin');

// Revenue and Stats
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?: 0;
$user_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$order_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$low_stock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock < 5 AND is_digital = FALSE")->fetchColumn();

include '../includes/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2.5rem;">
    <h2 style="font-size: 2rem; font-weight: 700; color: var(--primary);">Admin Dashboard</h2>
    <p style="color: var(--text-muted);">Welcome back, System Overview and Control</p>
</div>

<div class="stats-grid">
    <div class="card stat-card" style="border-left: 5px solid #10b981; background: #f0fdf4;">
        <h3>ÂRs. <?php echo number_format($revenue, 2); ?></h3>
        <p>Total Revenue</p>
    </div>
    <div class="card stat-card" style="border-left: 5px solid var(--accent); background: #eff6ff;">
        <h3><?php echo $user_count; ?></h3>
        <p>Total Users</p>
    </div>
    <div class="card stat-card" style="border-left: 5px solid #f59e0b; background: #fffbeb;">
        <h3><?php echo $order_count; ?></h3>
        <p>Total Orders</p>
    </div>
    <div class="card stat-card" style="border-left: 5px solid var(--secondary); background: #fff1f2;">
        <h3><?php echo $low_stock; ?></h3>
        <p>Low Stock Alerts</p>
    </div>
</div>

<h3 style="margin-bottom: 1.5rem; font-weight: 600;">System Management</h3>
<div class="action-grid">
    <div class="card action-card">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">ðŸ‘¥</div>
        <h4>User Control</h4>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Manage user permissions and roles.</p>
        <a href="manage_useRs. php" class="btn btn-primary" style="width: 100%;">Access Users</a>
    </div>
    <div class="card action-card">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">ðŸ“¦</div>
        <h4>Order Tracking</h4>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Monitor and update customer ordeRs. </p>
        <a href="manage_ordeRs. php" class="btn btn-primary" style="width: 100%;">View Orders</a>
    </div>
    <div class="card action-card">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">ðŸŽ¸</div>
        <h4>Inventory</h4>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Update stock and product details.</p>
        <a href="manage_products.php" class="btn btn-primary" style="width: 100%;">Catalog Management</a>
    </div>
    <div class="card action-card">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">ðŸŒ</div>
        <h4>Main Site</h4>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Preview the public shop front.</p>
        <a href="../index.php" class="btn btn-secondary" style="width: 100%;">View Shop</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
