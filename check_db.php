<?php
// check_db.php
require 'includes/db.php';
$stmt = $pdo->query('SELECT COUNT(*) FROM orders');
echo 'Orders: ' . $stmt->fetchColumn() . "\n";
$stmt = $pdo->query('SELECT * FROM orders ORDER BY id DESC LIMIT 1');
print_r($stmt->fetch());
?>
