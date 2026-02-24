<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole('Admin');

// Handle Product Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category_id'];
    $stock = $_POST['stock'];
    $brand = $_POST['brand'];
    $is_digital = isset($_POST['is_digital']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id, stock, brand, is_digital) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $cat, $stock, $brand, $is_digital]);
    $msg = "Product added successfully!";
}

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

include '../includes/header.php';
?>

<h2>Manage Products</h2>

<?php if (isset($msg)) echo "<p style='color: green;'>$msg</p>"; ?>

<div class="card">
    <h3>Add New Product</h3>
    <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
        <div>
            <label>Name</label><br>
            <input type="text" name="name" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div>
            <label>Category</label><br>
            <select name="category_id" style="width: 100%; padding: 0.5rem;" required>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="grid-column: span 2;">
            <label>Description</label><br>
            <textarea name="description" style="width: 100%; padding: 0.5rem;" rows="3"></textarea>
        </div>
        <div>
            <label>Price (ÂRs. )</label><br>
            <input type="number" step="0.01" name="price" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div>
            <label>Stock</label><br>
            <input type="number" name="stock" value="0" style="width: 100%; padding: 0.5rem;" required>
        </div>
        <div>
            <label>Brand</label><br>
            <input type="text" name="brand" style="width: 100%; padding: 0.5rem;">
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" name="is_digital" id="is_digital">
            <label for="is_digital">Digital Product</label>
        </div>
        <div style="grid-column: span 2;">
            <button type="submit" name="add_product" class="btn btn-primary" style="width: 100%;">Add Product</button>
        </div>
    </form>
</div>

<table class="cart-table" style="margin-top: 2rem;">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo $p['name']; ?></td>
                <td>ÂRs. <?php echo number_format($p['price'], 2); ?></td>
                <td><?php echo $p['is_digital'] ? 'N/A' : $p['stock']; ?></td>
                <td><?php echo $p['is_digital'] ? 'Digital' : 'Physical'; ?></td>
                <td>
                    <button class="btn" style="background: grey; color: white; padding: 0.3rem 0.6rem;">Edit</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>
