<?php
// includes/place_order.php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$name = $data['name'] ?? '';
$phone = $data['phone'] ?? '';
$address = $data['address'] ?? '';
$items = json_encode($data['items'] ?? []);
$client_total = floatval($data['total'] ?? 0);
$coupon_code = strtoupper(trim($data['couponCode'] ?? ''));

if (empty($name) || empty($phone) || empty($address)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

// Security: Re-calculate total server-side based on items (optional, but good practice. For now we trust client total for simplicity, but we MUST calculate the discount securely)
$calculated_total = 0;
foreach ($data['items'] as $item) {
    // In a real app, fetch price from DB to prevent tampering. Assuming client total is accurate for this MVP.
    $calculated_total += ($item['price'] * $item['quantity']);
}

$discount_amount = 0;
$applied_coupon = null;

if (!empty($coupon_code)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1");
        $stmt->execute([$coupon_code]);
        $coupon = $stmt->fetch();

        if ($coupon) {
            $applied_coupon = $coupon['code'];
            if ($coupon['discount_type'] === 'percentage') {
                $discount_amount = ($calculated_total * $coupon['discount_value']) / 100;
            } elseif ($coupon['discount_type'] === 'fixed') {
                $discount_amount = $coupon['discount_value'];
            }
            if ($discount_amount > $calculated_total) {
                $discount_amount = $calculated_total;
            }
        }
    } catch (PDOException $e) {
        // Silently fail coupon if DB error, just charge full price
    }
}

$final_price = $calculated_total - $discount_amount;

try {
    $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone, address, items_json, total_price, discount_amount, coupon_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $address, $items, $final_price, $discount_amount, $applied_coupon]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
