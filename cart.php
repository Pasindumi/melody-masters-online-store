<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

// Handle updates/removals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $id => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $qty;
            }
        }
    }
    if (isset($_POST['remove_item'])) {
        $id = $_POST['product_id'];
        unset($_SESSION['cart'][$id]);
    }
    redirect('cart.php');
}

include 'includes/header.php';

$cart_items = [];
$subtotal = 0;
$has_physical = false;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $item_total = $product['price'] * $qty;
        $subtotal += $item_total;
        if (!$product['is_digital']) $has_physical = true;
        
        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $qty,
            'total' => $item_total,
            'is_digital' => $product['is_digital']
        ];
    }
}

// Shipping Business Rules (LKR)
$shipping = 0;
if ($has_physical) {
    $shipping = ($subtotal > 5000) ? 0 : 500;
}
$grand_total = $subtotal + $shipping;
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-size: 2rem; font-weight: 700; color: var(--primary);">Your Shopping Cart</h2>
    <p style="color: var(--text-muted);">Review your items before checkout</p>
</div>

<?php if (empty($cart_items)): ?>
    <div class="card" style="text-align: center; padding: 3rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">ðŸ›’</div>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Your cart is currently empty.</p>
        <a href="shop.php" class="btn btn-primary">Go to Shop</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
        <div class="table-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <form id="cart-form" method="POST">
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600;"><?php echo $item['name']; ?></div>
                                <?php if ($item['is_digital']): ?>
                                    <span style="font-size: 0.75rem; color: var(--accent); background: #eff6ff; padding: 2px 8px; border-radius: 10px;">Digital</span>
                                <?php endif; ?>
                            </td>
                            <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" style="width: 70px; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px;">
                            </td>
                            <td style="font-weight: 600;">Rs. <?php echo number_format($item['total'], 2); ?></td>
                            <td>
                                <button type="submit" name="remove_item" form="remove-<?php echo $item['id']; ?>" class="btn" style="background: #fdf2f2; color: var(--secondary); padding: 0.5rem; border-radius: 8px;">
                                    ðŸ—‘ï¸
                                </button>
                                <form id="remove-<?php echo $item['id']; ?>" method="POST" style="display:none;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="remove_item" value="1">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="padding: 1.5rem; border-top: 1px solid var(--border); text-align: right;">
                <button type="submit" name="update_cart" form="cart-form" class="btn btn-secondary">Update Quantities</button>
            </div>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Order Summary</h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <span style="color: var(--text-muted);">Subtotal</span>
                <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <span style="color: var(--text-muted);">Shipping</span>
                <span style="color: <?php echo $shipping == 0 ? '#10b981' : 'inherit'; ?>; font-weight: <?php echo $shipping == 0 ? '600' : 'normal'; ?>;">
                    <?php echo $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2); ?>
                </span>
            </div>
            <?php if ($shipping > 0): ?>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">Free shipping on orders over Rs. 5,000.00</p>
            <?php endif; ?>
            <hr style="margin-bottom: 1rem; border: none; border-top: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; align-items: center;">
                <span style="font-weight: 700; font-size: 1.1rem;">Total</span>
                <span style="font-weight: 700; font-size: 1.25rem; color: var(--primary);">Rs. <?php echo number_format($grand_total, 2); ?></span>
            </div>
            <a href="checkout.php" class="btn btn-primary" style="width: 100%; justify-content: center;">Proceed to Checkout</a>
            <a href="shop.php" class="btn btn-secondary" style="width: 100%; margin-top: 0.75rem; justify-content: center;">Continue Shopping</a>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
