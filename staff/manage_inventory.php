<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $id = $_POST['product_id'];
    $new_stock = (int)$_POST['stock'];
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$new_stock, $id]);
}

$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_digital = FALSE ORDER BY p.stock ASC");
$products = $stmt->fetchAll();

include '../includes/header.php';
?>

<h2>Manage Inventory</h2>

<table class="cart-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Update Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr style="<?php echo $p['stock'] < 5 ? 'background: #fff3f3;' : ''; ?>">
                <td><?php echo $p['name']; ?></td>
                <td><?php echo $p['category_name']; ?></td>
                <td><strong><?php echo $p['stock']; ?></strong></td>
                <td>
                    <form method="POST" style="display: flex; gap: 0.5rem;">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="number" name="stock" value="<?php echo $p['stock']; ?>" min="0" style="width: 70px; padding: 0.3rem;">
                        <button type="submit" name="update_stock" class="btn btn-primary" style="padding: 0.3rem 0.6rem;">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>
