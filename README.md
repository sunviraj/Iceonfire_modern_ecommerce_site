# Iceonfire | Modern Gourmet Frozen Foods E-Commerce Site

Iceonfire is a premium, modern e-commerce web application specifically built for ordering frozen foods, sauces, lollipops, and snacks. Designed with rich aesthetics, glassmorphism, responsive navigation, and vibrant gradients, it delivers a smooth shopping experience for users, alongside a robust administration dashboard for managing inventory and orders.

---

## 🌟 Key Features

### Frontend (Customer Experience)
*   **Stunning Modern UI**: Implements a dark/light contrasted theme using Outfit and Inter fonts, responsive navigation, glassmorphic headers, and harmonious HSL-tailored color gradients representing "Ice" (cool blues) and "Fire" (warm pink/reds).
*   **Dual-State Product Hover**: Product cards feature a dynamic hover micro-animation that transitions from a "frozen/raw" product image to a "cooked/ready-to-serve" state.
*   **Responsive Side Cart Drawer**: A slide-out shopping cart for adding items, adjusting quantities, and automatically calculating the total.
*   **Interactive Product Detail Modals**: Customers can click cards to open an immersive details modal showcasing category badges, description, price, and item add options.
*   **Secure Checkout & Coupons**: Multi-stage checkout form accepting customer details for Cash on Delivery (COD). Features client-side AJAX requests validating discount coupons (supports fixed and percentage discounts) in real-time.

### Backend (Admin Portal)
*   **Admin Dashboard (`/admin`)**: A comprehensive panel secured with hash-based authentication (`password_hash`).
*   **Catalog Management**: Full CRUD interface for adding, editing, and deleting products (with custom categories, badges, raw and cooked images, and availability toggles).
*   **Order Tracking**: Real-time management of orders. Administrators can view ordered items, see calculated totals, track applied coupon codes, and update order statuses (`Pending`, `Delivered`, `Cancelled`).
*   **Coupon Management**: Panel to create, manage, and toggle the status (active/inactive) of custom coupon codes with percentage or flat rate discounts.
*   **Dynamic Settings Manager**: Modify website text, physical address details, support phone numbers, and change the hero banner background dynamically.

---

## 🛠️ Technology Stack

*   **Core Logic & Templating**: PHP (OOP-friendly procedural scripting)
*   **Database**: SQLite (`data/iceonfire.sqlite`) driven via PDO (PHP Data Objects)
*   **Styling**: Vanilla CSS (Premium animations, custom glassmorphism utilities, grid layouts)
*   **Interactions**: Vanilla JavaScript (AJAX/Fetch API for coupon validation and checkout handling, LocalStorage sync for the shopping cart)
*   **Server Compatibility**: Compatible with Apache/Nginx (includes `.htaccess` configuration)

---

## 📂 Project Structure

```
Iceonfire/
├── admin/                        # Admin dashboard files
│   ├── add_coupon.php            # Form/process to add coupons
│   ├── add_product.php           # Form/process to add products
│   ├── edit_product.php          # Form/process to edit products
│   ├── coupons.php               # Coupon list & status updates
│   ├── index.php                 # Admin dashboard entrypoint
│   ├── login.php                 # Admin login portal
│   └── logout.php                # Admin session logout
├── api/                          # REST API endpoints
│   └── validate_coupon.php       # POST endpoint validating coupons
├── assets/                       # Static front-end assets
│   ├── css/
│   │   └── style.css             # Main stylesheet (vars, layouts, transitions)
│   ├── js/
│   │   └── cart.js               # Cart logic, checkout, coupon AJAX
│   └── images/                   # Store images
├── data/                         # Local SQLite storage
│   └── iceonfire.sqlite          # SQLite database container
├── includes/                     # Reusable templates & backend controllers
│   ├── db.php                    # Database configuration & initialization helper
│   ├── place_order.php           # Order submission processor
│   └── product_card.php          # Modular product card HTML component
├── index.php                     # Frontend store homepage
├── init_db.php                   # Database structure seeding script
└── populate_real_products.php    # Mock catalog generator
```

---

## 🚀 Setup & Installation Instructions

Follow these steps to run the Iceonfire e-commerce application locally:

### Prerequisites
*   **PHP 8.0+** installed on your system.
*   **SQLite PHP Extension** enabled (typically enabled by default, check your `php.ini` file for `extension=pdo_sqlite` or `extension=sqlite3`).
*   A local development environment like XAMPP, Laragon, or the PHP built-in server.

### Steps
1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/sunviraj/Iceonfire_modern_ecommerce_site.git
    cd Iceonfire_modern_ecommerce_site
    ```

2.  **Initialize the Database**:
    Run the database initialization script using the PHP CLI to create the SQLite tables and insert default values:
    ```bash
    php init_db.php
    ```

3.  **Populate Catalog (Optional)**:
    If you want to populate the catalog with real products:
    ```bash
    php populate_real_products.php
    ```

4.  **Start the Local PHP Server**:
    Start PHP's built-in web server pointing to the root directory:
    ```bash
    php -S localhost:8000
    ```

5.  **Access the Application**:
    *   **Customer Frontend**: Open [http://localhost:8000](http://localhost:8000) in your browser.
    *   **Admin Dashboard**: Navigate to [http://localhost:8000/admin](http://localhost:8000/admin) to log in.

---

## 🔒 Default Administrator Credentials

Upon running `init_db.php`, a default administrator account is registered securely in the database.
*   **Username**: `admin`
*   **Password**: Whatsapp - 01626231443

> [!WARNING]
> It is highly recommended to change the password or username in the SQLite database (`admins` table) before deploying the application to a production server.

---

## 🎫 Coupon System Configuration

To add coupon discounts for checkout testing:
1.  Log in to the **Admin Dashboard**.
2.  Click on the **Coupons** link at the top-right of the navigation bar.
3.  Click **Add Coupon** to define:
    *   **Coupon Code** (e.g., `WINTER50` or `WELCOME10`)
    *   **Discount Type** (Percentage `%` or Fixed Amount `৳`)
    *   **Discount Value** (Amount or percent value)
    *   **Activation Status** (Toggle active/inactive)

---

## 📜 License

This project is licensed under the MIT License.
