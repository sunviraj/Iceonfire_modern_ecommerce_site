// assets/js/cart.js

let cart = JSON.parse(localStorage.getItem('iceonfire_cart')) || [];
let appliedCoupon = null;
let currentTotal = 0;


function updateCartUI() {
    const cartCount = document.getElementById('cart-count');
    const cartItems = document.getElementById('cart-items');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    
    // Update count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.innerText = totalItems;
    
    // Update items list
    cartItems.innerHTML = '';
    let total = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        cartItems.innerHTML += `
            <div class="cart-item">
                <div style="display:flex; gap: 10px; align-items:center;">
                    <img src="${item.image}" style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
                    <div>
                        <div style="font-weight:600;">${item.name}</div>
                        <div style="font-size:0.8rem; color:#666;">৳${item.price} x ${item.quantity}</div>
                    </div>
                </div>
                <div>
                    <button onclick="changeQuantity(${index}, -1)" style="padding:2px 8px;">-</button>
                    <button onclick="changeQuantity(${index}, 1)" style="padding:2px 8px;">+</button>
                </div>
            </div>
        `;
    });
    
    cartTotalAmount.innerText = `৳${total.toFixed(2)}`;
    localStorage.setItem('iceonfire_cart', JSON.stringify(cart));
}

function addToCart(id, name, price, image) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id, name, price, image, quantity: 1 });
    }
    updateCartUI();
    toggleCart(true); // Open cart when item added
}

function changeQuantity(index, delta) {
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    updateCartUI();
}

function toggleCart(forceOpen = null) {
    const modal = document.getElementById('cart-modal');
    const overlay = document.querySelector('.cart-overlay');
    
    if (forceOpen === true) {
        modal.classList.add('active');
        overlay.classList.add('active');
    } else if (forceOpen === false) {
        modal.classList.remove('active');
        overlay.classList.remove('active');
    } else {
        modal.classList.toggle('active');
        overlay.classList.toggle('active');
    }
}

function showCheckout() {
    if (cart.length === 0) {
        alert("Your cart is empty!");
        return;
    }
    toggleCart(false);
    
    // Reset coupon state on open
    appliedCoupon = null;
    document.getElementById('coupon-code-input').value = '';
    document.getElementById('discount-row').style.display = 'none';
    document.getElementById('coupon-message').innerText = '';
    
    currentTotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('checkout-subtotal').innerText = `৳${currentTotal.toFixed(2)}`;
    document.getElementById('checkout-final-total').innerText = `৳${currentTotal.toFixed(2)}`;
    
    document.getElementById('checkout-modal').style.display = 'flex';
    document.querySelector('.cart-overlay').classList.add('active');
}

function hideCheckout() {
    document.getElementById('checkout-modal').style.display = 'none';
    document.querySelector('.cart-overlay').classList.remove('active');
}

async function applyCoupon() {
    const codeInput = document.getElementById('coupon-code-input').value.trim();
    const msg = document.getElementById('coupon-message');
    
    if (!codeInput) {
        msg.innerText = "Please enter a coupon code.";
        msg.style.color = "red";
        return;
    }
    
    msg.innerText = "Validating...";
    msg.style.color = "#666";
    
    try {
        const response = await fetch('api/validate_coupon.php', {
            method: 'POST',
            body: JSON.stringify({ code: codeInput, total: currentTotal }),
            headers: { 'Content-Type': 'application/json' }
        });
        
        const result = await response.json();
        
        if (result.success) {
            appliedCoupon = {
                code: result.coupon_code,
                discount: result.discount_amount
            };
            
            document.getElementById('discount-row').style.display = 'flex';
            document.getElementById('discount-code-label').innerText = result.coupon_code;
            document.getElementById('checkout-discount').innerText = `-৳${result.discount_amount.toFixed(2)}`;
            document.getElementById('checkout-final-total').innerText = `৳${result.new_total.toFixed(2)}`;
            
            msg.innerText = "Coupon applied successfully!";
            msg.style.color = "green";
        } else {
            msg.innerText = result.error;
            msg.style.color = "red";
            
            // Reset discount
            appliedCoupon = null;
            document.getElementById('discount-row').style.display = 'none';
            document.getElementById('checkout-final-total').innerText = `৳${currentTotal.toFixed(2)}`;
        }
    } catch (e) {
        msg.innerText = "Error applying coupon.";
        msg.style.color = "red";
    }
}

async function submitOrder(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const orderData = {
        name: formData.get('name'),
        phone: formData.get('phone'),
        address: formData.get('address'),
        items: cart,
        total: currentTotal,
        couponCode: appliedCoupon ? appliedCoupon.code : null
    };

    try {
        const response = await fetch('includes/place_order.php', {
            method: 'POST',
            body: JSON.stringify(orderData),
            headers: { 'Content-Type': 'application/json' }
        });
        
        const result = await response.json();
        if (result.success) {
            alert("Order placed successfully! We will contact you soon.");
            cart = [];
            updateCartUI();
            hideCheckout();
        } else {
            alert("Failed to place order: " + result.error);
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Something went wrong. Please try again.");
    }
}

function openProductModal(product) {
    document.getElementById('m-image').src = product.image_url;
    document.getElementById('m-name').innerText = product.name;
    document.getElementById('m-desc').innerText = product.description;
    document.getElementById('m-price').innerText = '৳' + parseFloat(product.price).toFixed(2);
    document.getElementById('m-badge').innerText = (product.featured_type && product.featured_type !== 'None') ? product.featured_type : 'Product';
    
    // Update add to cart button in modal
    document.getElementById('m-add-btn').onclick = () => {
        addToCart(product.id, product.name, product.price, product.image_url);
        closeProductModal();
    };
    
    document.getElementById('product-modal').style.display = 'flex';
}

function closeProductModal() {
    document.getElementById('product-modal').style.display = 'none';
}

// Initial UI update
updateCartUI();
