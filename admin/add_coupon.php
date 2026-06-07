<?php
// admin/add_coupon.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    
    if (empty($code) || empty($discount_value)) {
        $error = "Code and Value are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_type, discount_value) VALUES (?, ?, ?)");
            $stmt->execute([$code, $discount_type, $discount_value]);
            header('Location: coupons.php?msg=added');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Unique constraint violation
                $error = "Coupon code already exists.";
            } else {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Coupon | Iceonfire Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f4f7f6; }
        .admin-header { background: #1a2a3a; color: white; padding: 1rem 5%; display: flex; justify-content: space-between; align-items: center; }
        .admin-header a { color: #00c6ff; text-decoration: none; font-weight: 600; }
        .container { max-width: 600px; margin: 3rem auto; padding: 0 5%; }
        .card { background: white; border-radius: 12px; padding: 2.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #333; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
        .error { color: #f5576c; margin-bottom: 1rem; font-weight: 600; background: #ffebee; padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="logo"><span class="ice">Ice</span><span class="fire">onfire</span></div>
    <div>
        <a href="coupons.php" style="margin-right: 15px;">Back to Coupons</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <h2 style="margin-bottom: 2rem;">Add New Coupon</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Coupon Code (e.g. SUMMER10, RAMANESH50)</label>
                <input type="text" name="code" required style="text-transform: uppercase;">
            </div>
            
            <div class="form-group">
                <label>Discount Type</label>
                <select name="discount_type" required>
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (৳)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Discount Value</label>
                <input type="number" name="discount_value" step="0.01" required>
            </div>
            
            <button type="submit" class="add-btn" style="width: 100%;">Create Coupon</button>
        </form>
    </div>
</div>

</body>
</html>
