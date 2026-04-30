<?php
$pageTitle = 'Dashboard';
require_once '../includes/admin_header.php';

$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn();
$revenue = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders")->fetchColumn();

$recentOrders = $pdo->query("SELECT o.*, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5")->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Products</h3>
        <div class="stat-value"><?php echo $productCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Orders</h3>
        <div class="stat-value"><?php echo $orderCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Total Customers</h3>
        <div class="stat-value"><?php echo $userCount; ?></div>
    </div>
    <div class="stat-card">
        <h3>Revenue</h3>
        <div class="stat-value"><?php echo formatPrice($revenue); ?></div>
    </div>
</div>

<h2 style="margin-bottom: 20px;">Recent Orders</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($recentOrders as $order): ?>
        <tr>
            <td>#<?php echo $order['id']; ?></td>
            <td><?php echo sanitize($order['user_name']); ?></td>
            <td><?php echo formatPrice($order['total_price']); ?></td>
            <td>
                <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; background: <?php echo $order['status'] == 'completed' ? '#e8f5e9' : '#fff3e0'; ?>; color: <?php echo $order['status'] == 'completed' ? '#2e7d32' : '#e65100'; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </td>
            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
