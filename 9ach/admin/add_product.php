<?php
$pageTitle = 'Add Product';
require_once '../includes/admin_header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $price = floatval($_POST['price']);
    $description = sanitize($_POST['description']);

    if (empty($name) || $price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        $imageName = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $imageName = uploadImage($_FILES['image']);
            if (!$imageName) {
                $error = 'Invalid image format. Use JPG, PNG, GIF, or WEBP.';
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, description, image, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($stmt->execute([$name, $price, $description, $imageName])) {
                setFlashMessage('Product added successfully', 'success');
                redirect('products.php');
            } else {
                $error = 'Failed to add product';
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
            <input type="text" name="name" required style="width: 100%; min-width: 320px;">
        </div>
        <div class="form-group">
            <label>Price *</label>
            <input type="number" name="price" step="0.01" min="0" required style="width: 100%; min-width: 320px;">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" style="width: 100%; min-width: 320px;"></textarea>
        </div>
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'imagePreview')" style="width: 100%; min-width: 320px;">
            <img id="imagePreview" style="display: none; max-width: 200px; margin-top: 10px; border-radius: 8px;">
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="submit" class="btn btn-primary">Add Product</button>
            <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
