<?php
// populate_real_products.php
require 'includes/db.php';

try {
    // Clear old products
    $pdo->exec("DELETE FROM products");
    echo "Old products cleared.\n";

    $products = [
        ['1 kg chicken ball (100+pc)', 'Premium quality chicken balls, perfect for snacks or meals.', 610, 'assets/images/chicken_ball_f.jpg', 'assets/images/chicken_ball_c.jpg', 'Frozen Snacks', 'Top Pick'],
        ['1 kg Chicken samosa', 'Traditional crispy chicken samosas with a delicious filling.', 610, 'assets/images/samosa_f.jpg', 'assets/images/samosa_c.jpg', 'Frozen Snacks', 'New Arrival'],
        ['1kg chicken sausages', 'Juicy and flavorful 1kg pack of chicken sausages.', 610, 'assets/images/sausages_f.jpg', 'assets/images/sausages_c.jpg', 'Frozen Snacks', 'Best Seller'],
        ['10pc pack sausages', 'Small convenient 10pc pack of chicken sausages.', 240, 'assets/images/sausages_f.jpg', 'assets/images/sausages_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg nuggets', 'Crispy chicken nuggets, kids and adults favorite.', 630, 'assets/images/nuggets_f.jpg', 'assets/images/nuggets_c.jpg', 'Frozen Snacks', 'Top Pick'],
        ['1kg chicken roll', 'Delicious chicken rolls ready to fry and serve.', 620, 'assets/images/samosa_f.jpg', 'assets/images/samosa_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg chicken lollipop', 'Specially prepared chicken lollipops for a gourmet treat.', 650, 'assets/images/lollipop_f.jpg', 'assets/images/lollipop_c.jpg', 'Frozen Snacks', 'New Arrival'],
        ['2.5kg hyfun french fries', 'Bulk pack of premium Hyfun french fries.', 1150, 'assets/images/fries_f.jpg', 'assets/images/fries_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg mozzarella cheese', 'High quality mozzarella cheese for the perfect stretch.', 750, 'assets/images/default.jpg', 'assets/images/default.jpg', 'Dairy', 'None'],
        ['Sliced vega cheese (84pc)', 'Quality sliced cheese for burgers and sandwiches.', 1520, 'assets/images/default.jpg', 'assets/images/default.jpg', 'Dairy', 'None'],
        ['20pc porota (1600gm)', 'Flaky and delicious 20pc pack of porotas.', 310, 'assets/images/default.jpg', 'assets/images/default.jpg', 'Frozen Snacks', 'Best Seller'],
        ['Dano cream can', 'Rich and creamy Dano cream can.', 170, 'assets/images/default.jpg', 'assets/images/default.jpg', 'Dairy', 'None']
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, cooked_image_url, category, featured_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    echo "Real products added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
