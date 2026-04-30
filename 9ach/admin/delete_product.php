<?php
require_once '../includes/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        if ($product['image'] && file_exists("../images/products/{$product['image']}")) {
            unlink("../images/products/{$product['image']}");
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);

        setFlashMessage('Product deleted successfully', 'success');
    }
}

redirect('products.php');
?>
