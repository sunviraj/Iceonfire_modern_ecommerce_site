<?php
// admin/coupons.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Handle delete/toggle
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete') {
        $pdo->prepare("DELETE FROM coupons WHERE id = ?")->execute([$id]);
    } elseif ($_GET['action'] === 'toggle') {
        $pdo->prepare("UPDATE coupons SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
    }
    header('Location: coupons.php');
    exit;
}

$coupons = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Coupons | Iceonfire Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f4f7f6; }
        .admin-header { background: #1a2a3a; color: white; padding: 1rem 5%; display: flex; justify-content: space-between; align-items: center; }
        .admin-header a { color: #00c6ff; text-decoration: none; font-weight: 600; }
        .container { max-width: 1200px; margin: 3rem auto; padding: 0 5%; }
        .card { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8fbff; font-weight: 600; }
        .btn-sm { padding: 5px 10px; border-radius: 5px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-right: 5px; }
        .btn-edit { background: #0072ff; color: white; }
        .btn-danger { background: #ff4d4d; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; color: white; }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="logo"><span class="ice">Ice</span><span class="fire">onfire</span> Admin</div>
    <div>
        <a href="index.php" style="margin-right: 15px;">Dashboard</a>
        <a href="coupons.php" style="margin-right: 15px;">Coupons</a>
        <a href="settings.php" style="margin-right: 15px;">Settings</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Coupons</h2>
            <a href="add_coupon.php" class="add-btn" style="width: auto; margin: 0; padding: 0.8rem 1.5rem;">+ Add New Coupon</a>
        </div>

        <?php if (empty($coupons)): ?>
            <p style="color: #666;">No coupons found. Create one above.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $c): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($c['code']); ?></strong></td>
                        <td style="text-transform: capitalize;"><?php echo htmlspecialchars($c['discount_type']); ?></td>
                        <td>
                            <?php 
                            if ($c['discount_type'] === 'percentage') {
                                echo htmlspecialchars($c['discount_value']) . '%';
                            } else {
                                echo '৳' . number_format($c['discount_value'], 2);
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($c['is_active']): ?>
                                <span class="badge" style="background: #28a745;">Active</span>
                            <?php else: ?>
                                <span class="badge" style="background: #6c757d;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="coupons.php?action=toggle&id=<?php echo $c['id']; ?>" class="btn-sm <?php echo $c['is_active'] ? 'btn-secondary' : 'btn-success'; ?>">
                                <?php echo $c['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </a>
                            <a href="coupons.php?action=delete&id=<?php echo $c['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this coupon?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
