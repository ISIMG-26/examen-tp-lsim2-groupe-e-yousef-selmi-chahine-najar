<?php
$pageTitle = 'Edit Product';
require_once '../includes/admin_header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('products.php');
}

$productId = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $price = floatval($_POST['price']);
    $description = sanitize($_POST['description']);

    if (empty($name) || $price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $imageName = $product['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $newImage = uploadImage($_FILES['image']);
            if ($newImage) {
                if ($imageName && file_exists("../images/products/$imageName")) {
                    unlink("../images/products/$imageName");
                }
                $imageName = $newImage;
            } else {
                $error = 'Invalid image format';
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
            if ($stmt->execute([$name, $price, $description, $imageName, $productId])) {
                setFlashMessage('Product updated successfully', 'success');
                redirect('products.php');
            } else {
                $error = 'Failed to update product';
            }
        }
    }
}
?>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="admin-product-form" style="max-width: 900px; width: 100%;">
    <form method="POST" enctype="multipart/form-data" style="width: 100%;">
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" value="<?php echo sanitize($product['name']); ?>" required style="width: 100%; min-width: 320px;">
        </div>
        <div class="form-group">
            <label>Price *</label>
            <input type="number" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required style="width: 100%; min-width: 320px;">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" style="width: 100%; min-width: 320px;"><?php echo sanitize($product['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <?php if ($product['image']): ?>
                <img src="../images/products/<?php echo $product['image']; ?>" 
                     style="max-width: 200px; margin-bottom: 10px; border-radius: 8px; display: block;">
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')" style="width: 100%; min-width: 320px;">
            <img id="imagePreview" style="display: none; max-width: 200px; margin-top: 10px; border-radius: 8px;">
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
