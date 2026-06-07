<?php
// fix_images.php
require 'includes/db.php';

try {
    $updates = [
        [
            'name' => '1kg mozzarella cheese',
            'img' => 'https://images.unsplash.com/photo-1631379410313-0570b57e754a?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Sliced vega cheese (84pc)',
            'img' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'Dano cream can',
            'img' => 'https://images.unsplash.com/photo-1628045618451-b84dc6ce88c8?auto=format&fit=crop&w=800&q=80'
        ]
    ];

    $stmt = $pdo->prepare("UPDATE products SET image_url = ?, cooked_image_url = ? WHERE name = ?");
    
    foreach ($updates as $u) {
        $stmt->execute([$u['img'], $u['img'], $u['name']]);
    }
    
    echo "Images updated successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
