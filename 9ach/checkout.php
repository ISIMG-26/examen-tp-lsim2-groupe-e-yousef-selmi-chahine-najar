<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('Please login to checkout', 'error');
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
if (empty($cart)) {
    setFlashMessage('Your cart is empty', 'error');
    redirect('shop.php');
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $orderId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
        }

        $pdo->commit();

        $_SESSION['cart'] = [];

        setFlashMessage('Order placed successfully! Order #' . $orderId, 'success');
        redirect('index.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        setFlashMessage('Error placing order: ' . $e->getMessage(), 'error');
    }
}

$pageTitle = 'Checkout';
include 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">Checkout</h2>

        <div style="max-width: 600px; margin: 0 auto;">
            <div class="cart-summary" style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 20px;">Order Summary</h3>
                <?php foreach ($cart as $item): ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid var(--border);">
                    <span><?php echo sanitize($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                    <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="cart-total" style="margin-top: 20px;">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
            </div>

            <form method="POST" class="form-container" style="margin: 0;">
                <h3 style="margin-bottom: 20px;">Shipping Information</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo sanitize($_SESSION['user_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo sanitize($_SESSION['user_email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" required placeholder="Enter your shipping address"></textarea>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" required placeholder="Enter your phone number">
                </div>

                <button type="submit" class="btn btn-primary w-full">Place Order (Cash on Delivery)</button>
                <p style="text-align: center; margin-top: 15px; font-size: 13px; color: var(--text-light);">
                    This is a demo store. No real payment will be processed.
                </p>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
