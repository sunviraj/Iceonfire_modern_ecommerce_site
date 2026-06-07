<?php
// update_products_final.php
require 'includes/db.php';

try {
    // Clear old products to ensure fresh start
    $pdo->exec("DELETE FROM products");
    
    $products = [
        ['1 kg chicken ball (100+pc)', 'Premium gourmet chicken balls.', 610, 'assets/images/chicken_ball_f.jpg', 'assets/images/chicken_ball_c.jpg', 'Frozen Snacks', 'Top Pick'],
        ['1 kg Chicken samosa', 'Traditional crispy chicken samosas.', 630, 'assets/images/samosa_f.jpg', 'assets/images/samosa_c.jpg', 'Frozen Snacks', 'New Arrival'],
        ['1kg chicken sausages', 'Flavorful 1kg pack of sausages.', 610, 'assets/images/sausages_f.jpg', 'assets/images/sausages_c.jpg', 'Frozen Snacks', 'Best Seller'],
        ['10pc pack sausages', 'Convenient 10pc sausage pack.', 210, 'assets/images/sausages_f.jpg', 'assets/images/sausages_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg nuggets', 'Crispy chicken nuggets.', 660, 'assets/images/nuggets_f.jpg', 'assets/images/nuggets_c.jpg', 'Frozen Snacks', 'Top Pick'],
        ['1kg chicken roll', 'Delicious chicken rolls.', 660, 'assets/images/samosa_f.jpg', 'assets/images/samosa_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg chicken lollipop', 'Gourmet chicken lollipops.', 590, 'assets/images/lollipop_f.jpg', 'assets/images/lollipop_c.jpg', 'Frozen Snacks', 'New Arrival'],
        ['2.5kg hyfun french fries', 'Premium french fries.', 1100, 'assets/images/fries_f.jpg', 'assets/images/fries_c.jpg', 'Frozen Snacks', 'None'],
        ['1kg mozzarella cheese', 'High-quality stretchy cheese.', 750, 'https://images.unsplash.com/photo-1631379410313-0570b57e754a?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1631379410313-0570b57e754a?auto=format&fit=crop&w=800&q=80', 'Dairy', 'None'],
        ['Sliced vega cheese (84pc)', 'Quality sliced cheese.', 1500, 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80', 'Dairy', 'None'],
        ['20pc porota (1600gm)', 'Flaky and delicious porotas.', 300, 'https://images.unsplash.com/photo-1628045618451-b84dc6ce88c8?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1628045618451-b84dc6ce88c8?auto=format&fit=crop&w=800&q=80', 'Frozen Snacks', 'Best Seller'],
        ['Dano cream can', 'Rich and creamy Dano cream.', 170, 'https://images.unsplash.com/photo-1628045618451-b84dc6ce88c8?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1628045618451-b84dc6ce88c8?auto=format&fit=crop&w=800&q=80', 'Dairy', 'None'],
        ['Baby corn', 'Fresh frozen baby corn.', 200, 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?auto=format&fit=crop&w=800&q=80', 'Vegetables', 'New Arrival'],
        ['Sweet corn', 'Golden sweet corn.', 200, 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?auto=format&fit=crop&w=800&q=80', 'Vegetables', 'None'],
        ['Mushroom', 'Premium sliced mushrooms.', 200, 'https://images.unsplash.com/photo-1504675099198-7023dd85f5a3?auto=format&fit=crop&w=800&q=80', 'https://images.unsplash.com/photo-1504675099198-7023dd85f5a3?auto=format&fit=crop&w=800&q=80', 'Vegetables', 'None']
    ];

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, cooked_image_url, category, featured_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute($p);
    }
    echo "<h1>Success!</h1><p>All products and prices updated. You can now delete this file.</p>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
