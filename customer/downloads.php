<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Customer');

// Digital products purchased and marked as 'Paid' or 'Delivered'
$stmt = $pdo->prepare("
    SELECT p.name, p.digital_file_path, o.id as order_id 
    FROM products p 
    JOIN order_items oi ON p.id = oi.product_id 
    JOIN orders o ON oi.order_id = o.id 
    WHERE o.user_id = ? AND p.is_digital = TRUE AND (o.status = 'Paid' OR o.status = 'Delivered')
");
$stmt->execute([$_SESSION['user_id']]);
$downloads = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>My Digital Downloads</h2>

<?php if (empty($downloads)): ?>
    <div class="card">
        <p>No digital products available for download.</p>
    </div>
<?php else: ?>
    <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
        <?php foreach ($downloads as $dl): ?>
            <div class="card">
                <h3><?php echo $dl['name']; ?></h3>
                <p>From Order #<?php echo $dl['order_id']; ?></p>
                <a href="../digital_files/<?php echo $dl['digital_file_path'] ?: 'sample.pdf'; ?>" class="btn btn-primary" style="margin-top: 1rem;" download>Download File</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>
