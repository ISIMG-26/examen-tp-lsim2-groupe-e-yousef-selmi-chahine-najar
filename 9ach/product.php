<?php
require_once 'includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('Product not found', 'error');
    redirect('shop.php');
}

$productId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlashMessage('Product not found', 'error');
    redirect('shop.php');
}

$pageTitle = $product['name'];
include 'includes/header.php';
?>

<section class="product-detail">
    <div class="product-detail-image">
        <img src="images/products/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
             alt="<?php echo sanitize($product['name']); ?>">
    </div>
    <div class="product-detail-info">
        <h1><?php echo sanitize($product['name']); ?></h1>
        <div class="product-detail-price"><?php echo formatPrice($product['price']); ?></div>
        <div class="product-detail-description">
            <?php echo nl2br(sanitize($product['description'])); ?>
        </div>

        <div class="quantity-selector">
            <button onclick="updateQuantity(-1)">-</button>
            <input type="number" id="quantity" value="1" min="1" max="99">
            <button onclick="updateQuantity(1)">+</button>
        </div>

        <button onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('quantity').value)" 
                class="btn btn-primary w-full">
            Add to Cart
        </button>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
