<?php
require_once 'config/db.php';
include 'includes/header.php';
?>

<section class="hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('uploads/hero.jpg'); background-size: cover; height: 400px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; border-radius: 15px; margin-bottom: 3rem;">
    <h2 style="font-size: 3rem; margin-bottom: 1rem;">Master Your Sound</h2>
    <p style="font-size: 1.2rem; margin-bottom: 2rem;">Discover the finest instruments at Melody MasteRs. </p>
    <a href="shop.php" class="btn btn-primary" style="font-size: 1.2rem;">Shop Now</a>
</section>

<section>
    <h2>Featured Products</h2>
    <div class="product-grid">
        <?php
        $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id LIMIT 4");
        while ($product = $stmt->fetch()):
        ?>
        <div class="product-card">
            <div class="product-img-wrapper" style="position: relative; overflow: hidden; height: 240px;">
                <img src="/melody-masters/uploads/<?php echo $product['image_url'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <span style="position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.9); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;"><?php echo $product['category_name']; ?></span>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;"><?php echo $product['brand']; ?></p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p class="price">ÂRs. <?php echo number_format($product['price'], 2); ?></p>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">View</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
