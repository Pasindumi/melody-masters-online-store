<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

requireLogin();

if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = '';
$success = false;

// Re-calculate totals
$subtotal = 0;
$has_physical = false;
$cart_details = [];

foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if ($product) {
        $item_total = $product['price'] * $qty;
        $subtotal += $item_total;
        if (!$product['is_digital']) $has_physical = true;
        $cart_details[] = array_merge($product, ['qty' => $qty]);
    }
}

// LKR Shipping rules
$shipping = $has_physical ? (($subtotal > 5000) ? 0 : 500) : 0;
$grand_total = $subtotal + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_cost, status) VALUES (?, ?, ?, 'Paid')");
        $stmt->execute([$_SESSION['user_id'], $grand_total, $shipping]);
        $order_id = $pdo->lastInsertId();

        // 2. Create Order Items & Update Stock
        foreach ($cart_details as $item) {
            // Check stock again for physical items
            if (!$item['is_digital'] && $item['stock'] < $item['qty']) {
                throw new Exception("Insufficient stock for " . $item['name']);
            }

            // Insert item
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['qty'], $item['price']]);

            // Reduce stock if not digital
            if (!$item['is_digital']) {
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['qty'], $item['id']]);
            }
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        $success = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="card" style="max-width: 700px; margin: 2rem auto;">
    <?php if ($success): ?>
        <div style="text-align: center; padding: 3rem;">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">ðŸŽ‰</div>
            <h2 style="font-size: 2rem; font-weight: 700; color: #10b981; margin-bottom: 1rem;">Order Placed Successfully!</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Thank you for your purchase. Your order ID is <strong>#<?php echo $order_id; ?></strong>.</p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="customer/ordeRs. php" class="btn btn-primary">My Orders</a>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    <?php else: ?>
        <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 2rem; color: var(--primary);">Checkout Summary</h2>
        
        <?php if ($error): ?>
            <div style="background: #fdf2f2; color: var(--secondary); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #fee2e2;">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="table-container" style="margin-bottom: 2rem;">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_details as $item): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;"><?php echo $item['name']; ?></div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">Rs. <?php echo number_format($item['price'], 2); ?> x <?php echo $item['qty']; ?></div>
                            </td>
                            <td style="text-align: right; font-weight: 600;">Rs. <?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="background: var(--bg); padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Subtotal</span>
                <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Shipping Fee</span>
                <span>Rs. <?php echo number_format($shipping, 2); ?></span>
            </div>
            <hr style="margin: 1rem 0; border: none; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700; font-size: 1.25rem;">Grand Total</span>
                <span style="font-weight: 700; font-size: 1.5rem; color: var(--accent);">Rs. <?php echo number_format($grand_total, 2); ?></span>
            </div>
        </div>

        <form method="POST">
            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.4;">
                    <input type="checkbox" required id="terms">
                    <label for="terms">I agree to the terms and conditions. Note: This is a demo application, no real payment will be processed.</label>
                </p>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; justify-content: center;">
                Confirm Order & Pay
            </button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
