<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $product_id = $_POST['product_id'];
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    // Final verification: user must have purchased the product
    $stmt = $pdo->prepare("SELECT 1 FROM orders o JOIN order_items oi ON o.id = oi.order_id WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'Delivered'");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id, $rating, $comment]);
        header("Location: product.php?id=$product_id&msg=Review+submitted!");
    } else {
        header("Location: product.php?id=$product_id&error=Review+forbidden.");
    }
}
?>
