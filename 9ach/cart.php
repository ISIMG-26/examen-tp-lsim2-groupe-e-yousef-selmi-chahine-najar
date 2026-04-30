<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
    $action = isset($_POST['action']) ? $_POST['action'] : 'add';
    $isAjax = isset($_POST['ajax']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    switch ($action) {
        case 'add':
            if ($productId <= 0) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'count' => getCartCount(),
                        'message' => 'Invalid product'
                    ]);
                    exit;
                }

                setFlashMessage('Invalid product', 'error');
                break;
            }

            $added = false;
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
                $added = true;
            } else {
                $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                if ($product) {
                    $_SESSION['cart'][$productId] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => $quantity
                    ];
                    $added = true;
                }
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => $added,
                    'count' => getCartCount(),
                    'message' => $added ? 'Added to cart' : 'Product not found'
                ]);
                exit;
            }

            setFlashMessage($added ? 'Product added to cart' : 'Product not found', $added ? 'success' : 'error');
            break;

        case 'update':
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
            setFlashMessage('Cart updated', 'success');
            break;

        case 'remove':
            unset($_SESSION['cart'][$productId]);
            setFlashMessage('Product removed from cart', 'success');
            break;

        case 'clear':
            $_SESSION['cart'] = [];
            setFlashMessage('Cart cleared', 'success');
            break;
    }

    if (!$isAjax) {
        redirect('cart.php');
    }
}

$pageTitle = 'Cart';
include 'includes/header.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$total = 0;
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">Shopping Cart</h2>

        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything yet.</p>
                <a href="shop.php" class="btn btn-primary mt-4">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <?php foreach ($cart as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <div class="cart-item">
                    <img src="images/products/<?php echo $item['image'] ?: 'placeholder.jpg'; ?>" 
                         alt="<?php echo sanitize($item['name']); ?>" class="cart-item-image">
                    <div class="cart-item-info">
                        <h3><?php echo sanitize($item['name']); ?></h3>
                        <div class="cart-item-price"><?php echo formatPrice($item['price']); ?></div>
                    </div>
                    <form method="POST" class="cart-item-quantity">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                               min="1" max="99" onchange="this.form.submit()">
                    </form>
                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                        <strong><?php echo formatPrice($subtotal); ?></strong>
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <div class="cart-total">
                        <span>Total</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline">Clear Cart</button>
                        </form>
                        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
