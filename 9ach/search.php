<?php
require_once 'includes/functions.php';
$pageTitle = 'Search';
include 'includes/header.php';

$search = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$products = [];

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm]);
    $products = $stmt->fetchAll();
}
?>

<section class="search-section">
    <div class="container">
        <h2 class="section-title">Search Products</h2>

        <form method="GET" class="search-form">
            <input type="text" name="q" value="<?php echo $search; ?>" 
                   placeholder="Search for products..." required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <?php if (!empty($search)): ?>
            <?php if (empty($products)): ?>
                <div class="text-center">
                    <p>No products found for "<?php echo $search; ?>"</p>
                </div>
            <?php else: ?>
                <p style="text-align: center; margin-bottom: 30px; color: var(--text-light);">
                    Found <?php echo count($products); ?> result(s) for "<?php echo $search; ?>"
                </p>
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
