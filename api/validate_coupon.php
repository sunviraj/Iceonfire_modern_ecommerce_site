<?php
// api/validate_coupon.php
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$code = strtoupper(trim($data['code'] ?? ''));
$total = floatval($data['total'] ?? 0);

if (empty($code)) {
    echo json_encode(['success' => false, 'error' => 'Coupon code is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if ($coupon) {
        $discount_amount = 0;
        
        if ($coupon['discount_type'] === 'percentage') {
            $discount_amount = ($total * $coupon['discount_value']) / 100;
        } elseif ($coupon['discount_type'] === 'fixed') {
            $discount_amount = $coupon['discount_value'];
        }

        // Ensure discount doesn't exceed total
        if ($discount_amount > $total) {
            $discount_amount = $total;
        }

        $new_total = $total - $discount_amount;

        echo json_encode([
            'success' => true,
            'discount_amount' => $discount_amount,
            'new_total' => $new_total,
            'coupon_code' => $coupon['code'],
            'discount_type' => $coupon['discount_type'],
            'discount_value' => $coupon['discount_value']
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid or inactive coupon code']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
