<?php
// update_schema_coupons.php
require 'includes/db.php';

try {
    // 1. Create coupons table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS coupons (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code VARCHAR(50) UNIQUE NOT NULL,
            discount_type VARCHAR(20) NOT NULL, -- 'percentage' or 'fixed'
            discount_value FLOAT NOT NULL,
            is_active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'coupons' created or already exists.\n";

    // 2. Add discount tracking to orders table
    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN discount_amount FLOAT DEFAULT 0.0");
        echo "Column 'discount_amount' added to orders.\n";
    } catch (PDOException $e) {
        echo "Note: 'discount_amount' might already exist.\n";
    }

    try {
        $pdo->exec("ALTER TABLE orders ADD COLUMN coupon_code VARCHAR(50)");
        echo "Column 'coupon_code' added to orders.\n";
    } catch (PDOException $e) {
        echo "Note: 'coupon_code' might already exist.\n";
    }

    echo "Schema update completed successfully.\n";
} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
?>
