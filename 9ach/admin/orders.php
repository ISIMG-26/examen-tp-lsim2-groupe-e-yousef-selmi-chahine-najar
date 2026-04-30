<?php
$pageTitle = 'Orders';
require_once '../includes/admin_header.php';

$orders = $pdo->query("SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC")->fetchAll();
?>

<h2 style="margin-bottom: 30px;">All Orders</h2>

<table class="data-table">
    <thead>
        <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?php echo $order['id']; ?></td>
            <td>
                <?php echo sanitize($order['user_name']); ?><br>
                <small style="color: var(--text-light);"><?php echo $order['user_email']; ?></small>
            </td>
            <td><?php echo formatPrice($order['total_price']); ?></td>
            <td>
                <form method="POST" action="update_order_status.php" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 4px; border: 1px solid var(--border);">
                        <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
            </td>
            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
            <td>
                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
