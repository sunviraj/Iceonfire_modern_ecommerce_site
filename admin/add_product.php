<?php
// admin/add_product.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $featured_type = $_POST['featured_type'];
    
    // Handle Frozen Image Upload
    $image_url = 'assets/images/default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'frozen_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/images/' . $filename);
        $image_url = 'assets/images/' . $filename;
    }

    // Handle Cooked Image Upload
    $cooked_image_url = '';
    if (isset($_FILES['cooked_image']) && $_FILES['cooked_image']['error'] === 0) {
        $ext = pathinfo($_FILES['cooked_image']['name'], PATHINFO_EXTENSION);
        $filename = 'cooked_' . time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['cooked_image']['tmp_name'], __DIR__ . '/../assets/images/' . $filename);
        $cooked_image_url = 'assets/images/' . $filename;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, cooked_image_url, category, featured_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image_url, $cooked_image_url, $category, $featured_type]);
        header('Location: /admin/index.php?msg=added');
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
    <title>Add Product | Admin</title>
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
        <h2>Add New Product</h2>
        <p style="color:red;"><?php echo $msg; ?></p>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="Sauces">Sauces</option>
                    <option value="Lollipops">Lollipops</option>
                    <option value="Frozen Snacks">Frozen Snacks</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="form-group">
                <label>Featured Type</label>
                <select name="featured_type">
                    <option value="None">None</option>
                    <option value="New Arrival">New Arrival</option>
                    <option value="Top Pick">Top Pick</option>
                    <option value="Best Seller">Best Seller</option>
                </select>
            </div>
            <div class="form-group">
                <label>Price (৳)</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Frozen State Image (Default)</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <div class="form-group">
                <label>Cooked State Image (On Hover)</label>
                <input type="file" name="cooked_image" accept="image/*">
            </div>
            <button type="submit" class="add-btn" style="width: 100%;">Add Product</button>
        </form>
    </div>
</body>
</html>
