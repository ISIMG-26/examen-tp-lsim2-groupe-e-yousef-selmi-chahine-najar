<?php
require_once 'includes/functions.php';
$pageTitle = 'Home';
include 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
$products = $stmt->fetchAll();
?>

<section class="hero">
    <div class="container">
        <h1>Minimal Living, Maximum Style</h1>
        <p>Discover curated essentials designed for modern life. Quality meets simplicity at 9ach.</p>
        <a href="shop.php" class="btn btn-primary">Shop Now</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">New Arrivals</h2>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <img src="images/products/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                             alt="<?php echo sanitize($product['name']); ?>">
                    </a>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php echo sanitize($product['name']); ?></h3>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-actions">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm">View</a>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary btn-sm">Add to Cart</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
