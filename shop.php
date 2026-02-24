<?php
require_once 'config/db.php';

$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$min_price = $_GET['min_price'] ?? 0;
$max_price = $_GET['max_price'] ?? 10000;

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
}
if ($brand) {
    $query .= " AND p.brand = ?";
    $params[] = $brand;
}
$query .= " AND p.price BETWEEN ? AND ?";
$params[] = $min_price;
$params[] = $max_price;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$brands = $pdo->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL")->fetchAll();

include 'includes/header.php';
?>

<div class="shop-container" style="margin-top: 1rem;">
    <aside class="filters" style="border: 1px solid var(--border);">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Refine Search</h3>
        <form method="GET">
            <div class="filter-group">
                <h4>Category</h4>
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <h4>Brand</h4>
                <select name="brand">
                    <option value="">All Brands</option>
                    <?php foreach ($brands as $b): ?>
                        <option value="<?php echo $b['brand']; ?>" <?php echo ($brand == $b['brand']) ? 'selected' : ''; ?>>
                            <?php echo $b['brand']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <h4>Price Range</h4>
                <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price; ?>">
                <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price; ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
            <a href="shop.php" style="display: block; text-align: center; margin-top: 1rem; color: var(--text-color);">Clear Filters</a>
        </form>
    </aside>

    <main style="flex: 1;">
        <h2>All Products</h2>
        <?php if (empty($products)): ?>
            <p>No products found matching your criteria.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-img-wrapper" style="position: relative; overflow: hidden; height: 180px;">
                        <img src="/melody-masters/uploads/<?php echo $product['image_url'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="product-info">
                        <h3 style="font-size: 1rem;"><?php echo $product['name']; ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 0.5rem;"><?php echo $product['brand']; ?></p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <p class="price" style="font-size: 1rem;">ÂRs. <?php echo number_format($product['price'], 2); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">View</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
