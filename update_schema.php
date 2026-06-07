<?php
// update_schema.php
require 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN featured_type TEXT DEFAULT 'None'");
    echo "Column featured_type added successfully.\n";
} catch (PDOException $e) {
    echo "Error or column already exists: " . $e->getMessage() . "\n";
}
?>
