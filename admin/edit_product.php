<?php
// admin/edit_product.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $featured_type = $_POST['featured_type'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Handle Frozen Image Upload
    $image_url = $product['image_url'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'frozen_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/images/' . $filename);
        $image_url = 'assets/images/' . $filename;
    }

    // Handle Cooked Image Upload
    $cooked_image_url = $product['cooked_image_url'];
    if (isset($_FILES['cooked_image']) && $_FILES['cooked_image']['error'] === 0) {
        $ext = pathinfo($_FILES['cooked_image']['name'], PATHINFO_EXTENSION);
        $filename = 'cooked_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['cooked_image']['tmp_name'], __DIR__ . '/../assets/images/' . $filename);
        $cooked_image_url = 'assets/images/' . $filename;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, image_url=?, cooked_image_url=?, category=?, is_available=?, featured_type=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $image_url, $cooked_image_url, $category, $is_available, $featured_type, $id]);
        header('Location: /admin/index.php?msg=updated');
        exit;
    } catch (PDOException $e) {
        $msg = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-card { max-width: 600px; margin: 50px auto; background: white; padding: 3rem; border-radius: 20px; box-shadow: var(--shadow); }
        .form-group { margin-bottom: 1.5rem; }
        input, textarea, select { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body style="background: #f4f7f9;">
    <div class="form-card">
        <a href="index.php" style="text-decoration:none; color:#0072ff; margin-bottom: 1rem; display:block;">← Back to Dashboard</a>
        <h2>Edit Product</h2>
        <p style="color:red;"><?php echo $msg; ?></p>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="Sauces" <?php if($product['category'] == 'Sauces') echo 'selected'; ?>>Sauces</option>
                    <option value="Lollipops" <?php if($product['category'] == 'Lollipops') echo 'selected'; ?>>Lollipops</option>
                    <option value="Frozen Snacks" <?php if($product['category'] == 'Frozen Snacks') echo 'selected'; ?>>Frozen Snacks</option>
                    <option value="Others" <?php if($product['category'] == 'Others') echo 'selected'; ?>>Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Featured Type</label>
                <select name="featured_type">
                    <option value="None" <?php if($product['featured_type'] == 'None') echo 'selected'; ?>>None</option>
                    <option value="New Arrival" <?php if($product['featured_type'] == 'New Arrival') echo 'selected'; ?>>New Arrival</option>
                    <option value="Top Pick" <?php if($product['featured_type'] == 'Top Pick') echo 'selected'; ?>>Top Pick</option>
                    <option value="Best Seller" <?php if($product['featured_type'] == 'Best Seller') echo 'selected'; ?>>Best Seller</option>
                </select>
            </div>
            <div class="form-group">
                <label>Price (৳)</label>
                <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Frozen State Image (Default)</label>
                <input type="file" name="image" accept="image/*">
                <p style="font-size: 0.8rem; color: #888;">Current: <?php echo $product['image_url']; ?></p>
            </div>
            <div class="form-group">
                <label>Cooked State Image (On Hover)</label>
                <input type="file" name="cooked_image" accept="image/*">
                <p style="font-size: 0.8rem; color: #888;">Current: <?php echo $product['cooked_image_url']; ?></p>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_available" <?php echo $product['is_available'] ? 'checked' : ''; ?>>
                    Available for Sale
                </label>
            </div>
            <button type="submit" class="add-btn">Update Product</button>
        </form>
    </div>
</body>
</html>
