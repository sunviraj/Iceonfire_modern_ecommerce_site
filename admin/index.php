<?php
// admin/index.php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_product']]);
    header('Location: index.php?msg=deleted');
    exit;
}

// Handle Order Status Update
if (isset($_POST['update_order_status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header('Location: index.php?msg=order_updated');
    exit;
}

// Handle Setting Update
if (isset($_POST['update_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key = ?");
        $stmt->execute([$value, $key]);
    }
    header('Location: index.php?msg=settings_updated');
    exit;
}

// Fetch Data
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) { $settings[$s['key']] = $s['value']; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Iceonfire</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-nav {
            background: white;
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .tab-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            background: none;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            border-bottom: 2px solid transparent;
        }
        .tab-btn.active {
            color: #0072ff;
            border-bottom-color: #0072ff;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body style="background: #f4f7f9;">

<div class="admin-nav">
    <div class="logo" style="font-size: 1.2rem;">
        <span class="ice">Ice</span><span class="fire">onfire</span> <small style="color:#888">Admin</small>
    </div>
    <div>
        <a href="coupons.php" style="text-decoration:none; color:#0072ff; font-weight:600; margin-right:15px;">Coupons</a>
        <a href="logout.php" style="text-decoration:none; color:#f5576c; font-weight:600;">Logout</a>
    </div>
</div>

<div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Dashboard</h1>
        <a href="add_product.php" class="add-btn" style="width:auto; padding: 0.8rem 1.5rem;">+ Add New Product</a>
    </div>

    <div style="display:flex; gap: 1rem; margin-bottom: 2rem;">
        <button class="tab-btn active" onclick="showTab('products')">Manage Products</button>
        <button class="tab-btn" onclick="showTab('orders')">Recent Orders</button>
        <button class="tab-btn" onclick="showTab('settings')">Site Settings</button>
    </div>

    <!-- Products Tab -->
    <div id="products-tab" class="tab-content">
        <div class="card">
            <h2>Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><img src="../<?php echo $p['image_url']; ?>" style="width:50px; height:50px; object-fit:cover; border-radius:8px;"></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                        <td>৳<?php echo $p['price']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" style="color:#0072ff; margin-right:1rem;">Edit</a>
                            <a href="?delete_product=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?')" style="color:#f5576c;">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Orders Tab -->
    <div id="orders-tab" class="tab-content" style="display:none;">
        <div class="card">
            <h2>Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?php echo $o['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($o['customer_name']); ?></strong><br>
                            <small><?php echo htmlspecialchars($o['address']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($o['phone']); ?></td>
                        <td>
                            ৳<?php echo number_format($o['total_price'], 2); ?>
                            <?php if (!empty($o['coupon_code'])): ?>
                                <br><small style="color: #28a745; font-weight: 600;">(Coupon: <?php echo htmlspecialchars($o['coupon_code']); ?>)</small>
                            <?php endif; ?>
                        </td>

                        <td>
                            <span class="status-badge status-<?php echo strtolower($o['status']); ?>">
                                <?php echo $o['status']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" onchange="this.form.submit()" style="padding:4px; border-radius:4px;">
                                    <option value="">Update Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Delivered">Delivered</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <input type="hidden" name="update_order_status" value="1">
                            </form>
                            <button onclick="viewOrderItems(<?php echo htmlspecialchars($o['items_json']); ?>)" style="padding:4px 8px; cursor:pointer;">Items</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Settings Tab -->
    <div id="settings-tab" class="tab-content" style="display:none;">
        <div class="card">
            <h2>Site Settings</h2>
            <form method="POST">
                <input type="hidden" name="update_settings" value="1">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Hero Banner Image URL (External Link)</label>
                    <input type="text" name="settings[banner_url]" value="<?php echo htmlspecialchars($settings['banner_url']); ?>" placeholder="https://example.com/image.jpg" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px;">
                    <p style="font-size:0.8rem; color:#888; margin-top:0.5rem;">If empty, the default ice gradient will be used.</p>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">About Us Text</label>
                    <textarea name="settings[about_text]" rows="4" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px;"><?php echo htmlspecialchars($settings['about_text']); ?></textarea>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Contact Phone Number</label>
                    <input type="text" name="settings[contact_phone]" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display:block; margin-bottom:0.5rem; font-weight:600;">Physical Address</label>
                    <textarea name="settings[contact_address]" rows="3" style="width:100%; padding:0.8rem; border:1px solid #ddd; border-radius:8px;"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="add-btn" style="width:auto; padding:0.8rem 2rem;">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tab + '-tab').style.display = 'block';
    event.currentTarget.classList.add('active');
}

function viewOrderItems(items) {
    let list = items.map(i => `${i.name} (${i.quantity}x) - ৳${i.price}`).join('\n');
    alert("Order Items:\n" + list);
}
</script>

</body>
</html>
