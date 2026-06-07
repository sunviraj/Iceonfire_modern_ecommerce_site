<?php
// init_db.php
require_once __DIR__ . '/includes/db.php';

echo "Initializing database...\n";
init_database($pdo);
echo "Database initialized successfully.\n";

// Add some sample products if empty
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
if ($stmt->fetchColumn() == 0) {
    echo "Adding sample products...\n";
    $samples = [
        ['Spicy Garlic Sauce', 'A rich, spicy garlic sauce perfect for dipping or marinating.', 150.00, 'assets/images/sauce_garlic.jpg', 'Sauces'],
        ['Creamy Lollipop - Strawberry', 'Deliciously creamy strawberry flavored frozen lollipop.', 40.00, 'assets/images/lollipop_strawberry.jpg', 'Lollipops'],
        ['Tangy Mango Sauce', 'Zesty mango sauce that adds a tropical kick to any meal.', 160.00, 'assets/images/sauce_mango.jpg', 'Sauces'],
        ['Frozen Spring Rolls', 'Crispy and delicious frozen spring rolls ready to fry.', 200.00, 'assets/images/spring_rolls.jpg', 'Frozen Snacks']
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category) VALUES (?, ?, ?, ?, ?)");
    foreach ($samples as $product) {
        $stmt->execute($product);
    }
    echo "Sample products added.\n";
}
?>
