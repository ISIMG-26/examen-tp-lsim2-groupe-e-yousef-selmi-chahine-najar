<?php
$pageTitle = 'View Order';
require_once '../includes/admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('orders.php');
}

$orderId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();
?>

<div style="max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2>Order #<?php echo $order['id']; ?></h2>
        <span style="padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 500; background: <?php echo $order['status'] == 'completed' ? '#e8f5e9' : '#fff3e0'; ?>; color: <?php echo $order['status'] == 'completed' ? '#2e7d32' : '#e65100'; ?>">
            <?php echo ucfirst($order['status']); ?>
        </span>
    </div>

    <div style="background: var(--white); padding: 24px; border-radius: var(--radius); margin-bottom: 24px; box-shadow: var(--shadow);">
        <h3 style="margin-bottom: 16px; font-size: 16px;">Customer Information</h3>
        <p><strong>Name:</strong> <?php echo sanitize($order['user_name']); ?></p>
        <p><strong>Email:</strong> <?php echo $order['user_email']; ?></p>
        <p><strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></p>
    </div>

    <h3 style="margin-bottom: 16px;">Order Items</h3>
    <table class="data-table" style="margin-bottom: 24px;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td style="display: flex; align-items: center; gap: 12px;">
                    <img src="../images/products/<?php echo $item['image'] ?: 'placeholder.jpg'; ?>" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <?php echo sanitize($item['product_name']); ?>
                </td>
                <td><?php echo formatPrice($item['price']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: right; font-size: 20px; font-weight: 700;">
        Total: <?php echo formatPrice($order['total_price']); ?>
    </div>

    <div style="margin-top: 30px;">
        <a href="orders.php" class="btn btn-outline">← Back to Orders</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
