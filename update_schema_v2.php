<?php
// update_schema_v2.php
require 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN cooked_image_url TEXT");
    echo "Column cooked_image_url added successfully.\n";
} catch (PDOException $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}
?>
