<!-- includes/product_card.php -->
<div class="product-card" onclick="openProductModal(<?php echo htmlspecialchars(json_encode($p)); ?>)">
    <div class="product-image-container">
        <?php if ($p['featured_type'] && $p['featured_type'] !== 'None'): ?>
            <div class="card-badge <?php echo strtolower(str_replace(' ', '-', $p['featured_type'])); ?>">
                <?php echo htmlspecialchars($p['featured_type']); ?>
            </div>
        <?php endif; ?>
        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-image frozen-img">
        <?php if (!empty($p['cooked_image_url'])): ?>
        <img src="<?php echo htmlspecialchars($p['cooked_image_url']); ?>" alt="Cooked <?php echo htmlspecialchars($p['name']); ?>" class="product-image cooked-img">
        <?php endif; ?>
    </div>
    <div class="product-info">
        <span class="product-category"><?php echo htmlspecialchars($p['category']); ?></span>
        <h3 class="product-name"><?php echo htmlspecialchars($p['name']); ?></h3>
        <div class="product-price">৳<?php echo number_format($p['price'], 2); ?></div>
        <button class="add-btn" onclick="event.stopPropagation(); addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo addslashes($p['image_url']); ?>')">
            Add to Cart
        </button>
    </div>
</div>
