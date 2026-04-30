<?php
require_once __DIR__ . '/functions.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$pageTitle = isset($pageTitle) ? $pageTitle : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - 9ach Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <h2>9ach Admin</h2>
            </div>
            <nav class="admin-nav">
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    Products
                </a>
                <a href="add_product.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>">
                    Add Product
                </a>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                    Orders
                </a>
                <a href="logout.php" class="logout-link">Logout</a>
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-header">
                <h1><?php echo $pageTitle; ?></h1>
                <div class="admin-user">
                    Welcome, <?php echo sanitize($_SESSION['admin_name']); ?>
                </div>
            </header>

            <div class="admin-content">
                <?php showFlashMessage(); ?>
