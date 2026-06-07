<?php
// update_settings_address.php
require 'includes/db.php';

try {
    // Check if contact_address already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE key = 'contact_address'");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES ('contact_address', 'haji chanmia boshot bari (JOMIDARGOLLI)<br>east khilbarirtek, vatara, Dhaka 1212')");
        $stmt->execute();
        echo "Contact address setting added successfully.\n";
    } else {
        echo "Contact address setting already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
