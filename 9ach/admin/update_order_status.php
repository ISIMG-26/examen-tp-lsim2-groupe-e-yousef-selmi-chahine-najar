<?php
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);

    $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];

    if (in_array($status, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        setFlashMessage('Order status updated', 'success');
    }
}

redirect('orders.php');
?>
