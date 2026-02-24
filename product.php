<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

$id = $_GET['id'] ?? null;
if (!$id) redirect('shop.php');

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) redirect('shop.php');

// Simple Add to Cart logic (will handle POST in cart.php or same page)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = (int)$_POST['quantity'];
    if ($qty > 0 && ($product['is_digital'] || $qty <= $product['stock'])) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
        $msg = "Product added to cart!";
    } else {
        $error = "Not enough stock available.";
    }
}

// Check if user can review (must be logged in and have purchased the product)
$can_review = false;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT 1 FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Delivered'");
    $stmt->execute([$_SESSION['user_id'], $id]);
    if ($stmt->fetch()) {
        $can_review = true;
    }
}

// Fetch reviews
$stmt = $pdo->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$id]);
$reviews = $stmt->fetchAll();

$avg_rating = 0;
if (count($reviews) > 0) {
    $total_rating = array_sum(array_column($reviews, 'rating'));
    $avg_rating = $total_rating / count($reviews);
}

include 'includes/header.php';
?>

<div class="card">
    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px;">
            <img src="/melody-masters/uploads/"<?php echo $product['image_url'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; border-radius: 10px;">
        </div>
        <div style="flex: 1.5; min-width: 300px;">
            <h2><?php echo $product['name']; ?></h2>
            <p style="color: grey; margin-bottom: 1rem;"><?php echo $product['category_name']; ?> | Brand: <?php echo $product['brand']; ?></p>
            <p style="font-size: 1.5rem; color: var(--secondary-color); font-weight: bold; margin-bottom: 1rem;">ÂRs. <?php echo number_format($product['price'], 2); ?></p>
            
            <p style="margin-bottom: 2rem;"><?php echo nl2br($product['description']); ?></p>
            
            <?php if ($product['is_digital']): ?>
                <p style="color: green; font-weight: bold; margin-bottom: 1rem;">Digital Product (Instant Download after purchase)</p>
            <?php else: ?>
                <p style="margin-bottom: 1rem;">Availability: <strong><?php echo ($product['stock'] > 0) ? $product['stock'] . ' in stock' : 'Out of stock'; ?></strong></p>
            <?php endif; ?>

            <?php if (isset($msg)) echo "<p style='color: green;'>$msg</p>"; ?>
            <?php if (isset($error)) echo "<p style='color: var(--secondary-color);'>$error</p>"; ?>

            <?php if ($product['is_digital'] || $product['stock'] > 0): ?>
                <form method="POST">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['is_digital'] ? 99 : $product['stock']; ?>" style="padding: 0.5rem; width: 60px;">
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <h3>Customer Reviews (<?php echo number_format($avg_rating, 1); ?>/5)</h3>
    <hr style="margin: 1rem 0;">
    
    <?php if ($can_review): ?>
        <div style="margin-bottom: 2rem; background: #f9f9f9; padding: 1.5rem; border-radius: 5px; border: 1px solid #ddd;">
            <h4>Leave a Review</h4>
            <form action="process_review.php" method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                <div style="margin-bottom: 1rem;">
                    <label>Rating (1-5)</label><br>
                    <select name="rating" style="padding: 0.5rem; width: 100px;" required>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Terrible</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Comment</label><br>
                    <textarea name="comment" style="width: 100%; padding: 0.5rem;" rows="3" required></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
        <p>No reviews yet. Be the first to buy and let us know what you think!</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div style="margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                <strong><?php echo $review['username']; ?></strong> - Rating: <?php echo $review['rating']; ?>/5
                <p><?php echo $review['comment']; ?></p>
                <small style="color: grey;"><?php echo $review['created_at']; ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
