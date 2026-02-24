<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Customer');

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <div class="card">
        <p>You haven't placed any orders yet.</p>
    </div>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($order['created_at'])); ?></td>
                    <td>ÂRs. <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><span style="padding: 0.3rem 0.6rem; border-radius: 5px; background: #eee;"><?php echo $order['status']; ?></span></td>
                    <td><a href="#" class="btn btn-primary" style="padding: 0.3rem 0.6rem;">Details</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>
