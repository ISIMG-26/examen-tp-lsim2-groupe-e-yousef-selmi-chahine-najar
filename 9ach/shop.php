<?php
require_once 'includes/functions.php';
$pageTitle = 'Shop';
include 'includes/header.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$totalStmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

$stmt = $pdo->prepare("SELECT * FROM products LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">All Products</h2>

        <?php if (empty($products)): ?>
            <div class="text-center">
                <p>No products available yet.</p>
            </div>
        <?php else: ?>
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

            <?php if ($totalPages > 1): ?>
            <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-outline'; ?> btn-sm">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
