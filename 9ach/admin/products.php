<?php
$pageTitle = 'Products';
require_once '../includes/admin_header.php';

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; gap: 24px; margin-bottom: 30px;">
    <h2>All Products</h2>
    <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <img src="../images/products/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                     alt="<?php echo sanitize($product['name']); ?>">
            </td>
            <td><?php echo sanitize($product['name']); ?></td>
            <td><?php echo formatPrice($product['price']); ?></td>
            <td>
                <div class="table-actions">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirmDelete()">Delete</a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
