<?php
// index.php
require_once __DIR__ . '/includes/db.php';

// Fetch Data
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) { $settings[$s['key']] = $s['value']; }

// Categorize Products
$new_arrivals = $pdo->query("SELECT * FROM products WHERE is_available = 1 AND featured_type = 'New Arrival' LIMIT 4")->fetchAll();
$top_picks = $pdo->query("SELECT * FROM products WHERE is_available = 1 AND featured_type = 'Top Pick' LIMIT 4")->fetchAll();
$best_sellers = $pdo->query("SELECT * FROM products WHERE is_available = 1 AND featured_type = 'Best Seller' LIMIT 4")->fetchAll();
$all_products = $pdo->query("SELECT * FROM products WHERE is_available = 1 ORDER BY category")->fetchAll();

$banner_style = $settings['banner_url'] ? "background: url('".htmlspecialchars($settings['banner_url'])."') center/cover no-repeat;" : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Iceonfire offers premium frozen foods, sauces, lollipops, and snacks in Gata Ramanesh. Fast delivery and top quality guaranteed.">
    <meta name="keywords" content="frozen food, chicken balls, samosa, nuggets, iceonfire, gata ramanesh, wholesale, premium snacks">
    <meta name="author" content="Iceonfire">
    <title>Iceonfire | Gourmet Frozen Foods</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        html { scroll-behavior: smooth; }
        .hero { <?php echo $banner_style; ?> }
        <?php if ($settings['banner_url']): ?>
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            z-index: 0;
        }
        .hero h1, .hero p, .hero a { position: relative; z-index: 1; }
        <?php endif; ?>
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 3000;
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            width: 90%;
            max-width: 800px;
            border-radius: 30px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            position: relative;
            animation: modalPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-close {
            position: absolute;
            top: 20px; right: 20px;
            font-size: 2rem;
            cursor: pointer;
            color: #333;
            z-index: 10;
        }
        .modal-image { width: 50%; object-fit: cover; background: #f0f0f0; }
        .modal-body { width: 50%; padding: 3rem; display: flex; flex-direction: column; }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 1rem;
            background: var(--fire-gradient);
            color: white;
        }

        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 400px;
            height: 100%;
            background: white;
            z-index: 2000;
            box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            padding: 2rem;
            flex-direction: column;
        }
        .cart-modal.active { display: flex; }
        .cart-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1500;
        }
        .cart-overlay.active { display: block; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid #eee; }
        .cart-total { margin-top: auto; padding-top: 2rem; border-top: 2px solid #eee; }
        .checkout-btn {
            background: var(--fire-gradient);
            width: 100%;
            padding: 1.2rem;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 800;
            cursor: pointer;
            margin-top: 1rem;
        }
        .close-cart { cursor: pointer; float: right; font-size: 1.5rem; }
        
        .about-section { background: white; padding: 7rem 5%; text-align: center; }
        .contact-section { background: #1a2a3a; color: white; padding: 7rem 5%; text-align: center; }
        
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 2rem; }
        
        @media (max-width: 768px) {
            .modal-content { flex-direction: column; height: 90vh; overflow-y: auto; }
            .modal-image, .modal-body { width: 100%; }
        }
    </style>
</head>
<body>

<header>
    <a href="/" style="text-decoration: none;">
        <div class="logo">
            <span class="ice">Ice</span><span class="fire">onfire</span>
        </div>
    </a>
    <div class="menu-toggle" onclick="toggleMenu()" style="display: none; font-size: 1.5rem; cursor: pointer; z-index: 1001;">☰</div>
    <ul class="nav-links" id="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="#products">Products</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
    <div class="cart-icon" onclick="toggleCart()">
        🛒 <span class="cart-count" id="cart-count">0</span>
    </div>
</header>

<section class="hero">
    <h1>Frozen Excellence, Delivered.</h1>
    <p>Premium sauces, lollipops, and frozen snacks available at your local departmental stores in Gata Ramanesh.</p>
    <a href="#products" class="add-btn" style="width: auto; padding: 1rem 2rem; text-decoration: none;">Explore Menu</a>
</section>

<!-- New Arrivals -->
<?php if ($new_arrivals): ?>
<div class="container">
    <h2 class="section-title">New Arrivals</h2>
    <div class="feature-grid">
        <?php foreach ($new_arrivals as $p): ?>
            <?php include 'includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Top Picks -->
<?php if ($top_picks): ?>
<div class="container" style="background: #f0f7ff; padding: 4rem 5%; border-radius: 40px; max-width: 1300px;">
    <h2 class="section-title">Top Picks for You</h2>
    <div class="feature-grid">
        <?php foreach ($top_picks as $p): ?>
            <?php include 'includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Best Sellers -->
<?php if ($best_sellers): ?>
<div class="container">
    <h2 class="section-title">Best Sellers</h2>
    <div class="feature-grid">
        <?php foreach ($best_sellers as $p): ?>
            <?php include 'includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- All Products -->
<div class="container" id="products">
    <h2 class="section-title">Our Full Collection</h2>
    <div class="product-grid">
        <?php foreach ($all_products as $p): ?>
            <?php include 'includes/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

<section class="about-section" id="about">
    <div class="container">
        <h2 class="section-title">Our Commitment to Freshness</h2>
        <p style="max-width: 900px; margin: 0 auto; font-size: 1.2rem; color: #555; line-height: 1.8;">
            At <strong>Iceonfire</strong>, we believe that the secret to great food lies in how it's preserved. 
            We maintain <strong>ultra-high standards</strong> in our freezing process, ensuring that every sauce, lollipop, and snack retains its original flavor, texture, and nutritional value. 
            Our advanced flash-freezing technology keeps our products as fresh as the day they were made.
            <br><br>
            We are also deeply committed to <strong>environmental sustainability</strong>. Our facility uses energy-efficient freezing systems, and we strive to minimize our carbon footprint while providing the best frozen delights to Gata Ramanesh.
        </p>
    </div>
</section>

<section class="contact-section" id="contact">
    <div class="container">
        <h2 class="section-title" style="color: white;">Professional Support</h2>
        <p style="font-size: 1.2rem; margin-bottom: 3rem; opacity: 0.8;">Whether you're a departmental store looking for wholesale or a customer with a question, our team is here to help.</p>
        
        <div style="display: flex; justify-content: center; gap: 4rem; flex-wrap: wrap;">
            <div>
                <h3 style="margin-bottom: 1rem; color: #00c6ff;">Direct Contact</h3>
                <div style="font-size: 1.5rem; font-weight: 800;">
                    <?php echo htmlspecialchars($settings['contact_phone']); ?>
                </div>
            </div>
            <div>
                <h3 style="margin-bottom: 1rem; color: #00c6ff;">Location</h3>
                <p style="font-size: 1.1rem;">
                    <?php echo isset($settings['contact_address']) ? $settings['contact_address'] : 'haji chanmia boshot bari (JOMIDARGOLLI)<br>east khilbarirtek, vatara, Dhaka 1212'; ?>
                </p>
            </div>
        </div>
        
        <p style="margin-top: 5rem; opacity: 0.4; font-size: 0.9rem;">© 2026 Iceonfire Frozen Foods. Built for Excellence.</p>
    </div>
</section>

<!-- Product Detail Modal -->
<div id="product-modal" class="modal" onclick="if(event.target == this) closeProductModal()">
    <div class="modal-content">
        <span class="modal-close" onclick="closeProductModal()">&times;</span>
        <img id="m-image" src="" class="modal-image">
        <div class="modal-body">
            <span id="m-badge" class="badge"></span>
            <h2 id="m-name" style="font-size: 2.5rem; margin-bottom: 1rem;"></h2>
            <div id="m-price" style="font-size: 1.8rem; font-weight: 800; color: #0072ff; margin-bottom: 1.5rem;"></div>
            <p id="m-desc" style="color: #666; font-size: 1.1rem; line-height: 1.6; flex-grow: 1;"></p>
            <button id="m-add-btn" class="add-btn" style="margin-top: 2rem;">Add to Cart</button>
        </div>
    </div>
</div>

<!-- Cart Modal -->
<div class="cart-overlay" onclick="toggleCart()"></div>
<div id="cart-modal" class="cart-modal">
    <div class="close-cart" onclick="toggleCart()">&times;</div>
    <h2>Your Cart</h2>
    <div id="cart-items" style="margin-top: 1.5rem; overflow-y: auto; max-height: 400px;"></div>
    <div class="cart-total">
        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 800;">
            <span>Total:</span>
            <span id="cart-total-amount">৳0.00</span>
        </div>
        <button class="checkout-btn" onclick="showCheckout()">Order Now (COD)</button>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkout-modal" class="modal">
    <div class="modal-content" style="max-width: 500px; flex-direction: column; padding: 2rem;">
        <span class="modal-close" onclick="hideCheckout()">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Complete Your Order</h2>
        <form id="checkout-form" onsubmit="submitOrder(event)">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Full Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Phone Number</label>
                <input type="text" name="phone" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label>Delivery Address</label>
                <textarea name="address" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; min-height: 80px;"></textarea>
            </div>
            
            <div style="background: #f8fbff; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal:</span>
                    <span id="checkout-subtotal">৳0.00</span>
                </div>
                <div id="discount-row" style="display: none; justify-content: space-between; margin-bottom: 10px; color: #f5576c;">
                    <span>Discount (<span id="discount-code-label"></span>):</span>
                    <span id="checkout-discount">-৳0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 1.2rem; border-top: 1px solid #ddd; padding-top: 10px;">
                    <span>Total:</span>
                    <span id="checkout-final-total">৳0.00</span>
                </div>
                
                <div style="margin-top: 1rem; display: flex; gap: 10px;">
                    <input type="text" id="coupon-code-input" placeholder="Coupon Code" style="flex-grow: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd; text-transform: uppercase;">
                    <button type="button" onclick="applyCoupon()" style="padding: 10px 15px; border-radius: 8px; background: #1a2a3a; color: white; border: none; cursor: pointer;">Apply</button>
                </div>
                <p id="coupon-message" style="font-size: 0.8rem; margin-top: 5px;"></p>
            </div>

            <p style="font-size: 0.9rem; color: #666; margin-bottom: 1.5rem;">Payment Method: <strong>Cash on Delivery</strong></p>
            <button type="submit" class="checkout-btn">Confirm Order</button>
        </form>
    </div>
</div>

<script src="assets/js/cart.js"></script>
<script>
    function toggleMenu() {
        document.getElementById('nav-links').classList.toggle('active');
    }
</script>
</body>
</html>
