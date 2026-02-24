<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Admin');

// Update order status logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-size: 2rem; font-weight: 700; color: var(--primary);">Order Management</h2>
    <p style="color: var(--text-muted);">View and update all customer orders</p>
</div>

<div class="table-container">
    <table class="cart-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong>#<?php echo $o['id']; ?></strong></td>
                    <td><?php echo $o['username']; ?></td>
                    <td style="font-weight: 600;">Rs. <?php echo number_format($o['total_amount'], 2); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status" style="padding: 0.4rem; border: 1px solid var(--border); border-radius: 6px; font-size: 0.9rem;">
                                <option value="Pending" <?php echo $o['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Paid" <?php echo $o['status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="Shipped" <?php echo $o['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?php echo $o['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?php echo $o['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 2rem;">
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>
