<?php
// includes/db.php

$db_path = __DIR__ . '/../data/iceonfire.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Helper to initialize the database
 */
function init_database($pdo) {
    // Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL,
        image_url TEXT,
        category TEXT,
        is_available INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Orders Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        address TEXT NOT NULL,
        items_json TEXT NOT NULL,
        total_price REAL NOT NULL,
        status TEXT DEFAULT 'Pending',
        payment_method TEXT DEFAULT 'COD',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Admin Users Table (Simple)
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL
    )");

    // Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )");

    // Default Settings
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES ('banner_url', '')");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES ('about_text', 'We provide the finest frozen foods in Gata Ramanesh.')");
    $stmt->execute();
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (key, value) VALUES ('contact_phone', '+880 1234 567890')");
    $stmt->execute();

    // Check if default admin exists...
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    if ($stmt->fetchColumn() == 0) {
        $username = 'admin';
        $password = 'iceonfire2024';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
    }
}
?>
