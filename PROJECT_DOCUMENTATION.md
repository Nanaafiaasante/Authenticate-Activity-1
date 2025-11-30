# VendorConnect Ghana - Complete Technical Documentation
**Project:** Wedding Planning E-Commerce Platform  
**Course:** CS442 – Electronic Commerce  
**Date:** November 29, 2025  
**Database:** ecommerce_2025A_nana_asante

---

## TABLE OF CONTENTS

1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Database Schema](#database-schema)
4. [Authentication System](#authentication-system)
5. [Core Features](#core-features)
6. [API Endpoints](#api-endpoints)
7. [Payment Integration](#payment-integration)
8. [File Structure](#file-structure)
9. [Setup Instructions](#setup-instructions)
10. [Security Features](#security-features)

---

## PROJECT OVERVIEW

### Business Model
VendorConnect Ghana is a two-sided marketplace connecting:
- **Wedding Planners (Vendors):** Create profiles, list services, manage bookings
- **Couples (Customers):** Browse planners, book services, make payments

### Key Value Propositions
1. **For Customers:**
   - Discover vetted wedding planners
   - Compare packages and prices
   - Secure online payments
   - Book consultations
   - Rate and review services

2. **For Vendors:**
   - Professional portfolio showcase
   - Subscription-based revenue model
   - Consultation booking management
   - Customer reviews and ratings
   - Analytics dashboard

### Revenue Streams
1. Vendor subscription fees (Starter: GHS 29/month, Premium: GHS 79/month)
2. Transaction fees on bookings (potential)
3. Featured listing upgrades (potential)
4. Consultation booking fees

---

## SYSTEM ARCHITECTURE

### Technology Stack

**Backend:**
- PHP 7.4+ (Server-side logic)
- MySQL 5.7+ (Database)
- Apache/MAMP (Web server)

**Frontend:**
- HTML5
- CSS3 (Custom + Bootstrap 5)
- JavaScript (Vanilla + AJAX)
- Bootstrap Icons

**External Services:**
- Paystack Payment Gateway (Ghana)
- Geolocation API (reverse geocoding)

### Architecture Pattern: Modified MVC

```
┌─────────────────────────────────────────────────────┐
│                    CLIENT BROWSER                    │
│              (HTML/CSS/JavaScript)                   │
└───────────────────┬─────────────────────────────────┘
                    │ HTTP/AJAX Requests
                    ↓
┌─────────────────────────────────────────────────────┐
│                  ENTRY POINTS                        │
│  ├── view/*.php (Customer pages)                    │
│  ├── admin/*.php (Vendor dashboard)                 │
│  ├── login/*.php (Auth pages)                       │
│  └── actions/*.php (API endpoints)                  │
└───────────────────┬─────────────────────────────────┘
                    │
                    ↓
┌─────────────────────────────────────────────────────┐
│                  CONTROLLERS                         │
│  Process requests, validate input, call models      │
│  ├── customer_controller.php                        │
│  ├── product_controller.php                         │
│  ├── cart_controller.php                            │
│  ├── order_controller.php                           │
│  └── consultation_controller.php                    │
└───────────────────┬─────────────────────────────────┘
                    │
                    ↓
┌─────────────────────────────────────────────────────┐
│                  CLASSES (MODELS)                    │
│  Business logic & database operations               │
│  ├── Customer (authentication, profiles)            │
│  ├── Product (CRUD, search, filter)                 │
│  ├── Cart (add, update, remove)                     │
│  ├── Order (create, track, invoice)                 │
│  └── Consultation (book, manage)                    │
└───────────────────┬─────────────────────────────────┘
                    │
                    ↓
┌─────────────────────────────────────────────────────┐
│              DATABASE CONNECTION                     │
│         db_connection (base class)                   │
│  ├── db_connect() - Establish connection            │
│  ├── db_query() - SELECT operations                 │
│  ├── db_write_query() - INSERT/UPDATE/DELETE        │
│  ├── db_fetch_one() - Single record                 │
│  └── db_fetch_all() - Multiple records              │
└───────────────────┬─────────────────────────────────┘
                    │
                    ↓
┌─────────────────────────────────────────────────────┐
│                  MySQL DATABASE                      │
│         ecommerce_2025A_nana_asante                 │
│  15+ tables with foreign key relationships          │
└─────────────────────────────────────────────────────┘
```

### Request Flow Example: Add to Cart

```
User clicks "Add to Cart"
    ↓
JavaScript (all_products.js)
    → fetch('../actions/add_to_cart_action.php')
    ↓
add_to_cart_action.php
    → Validates input
    → Calls add_to_cart_ctr()
    ↓
cart_controller.php
    → add_to_cart_ctr($p_id, $ip_add, $c_id, $qty)
    → Calls Cart class method
    ↓
cart_class.php
    → add_to_cart($p_id, $ip_add, $c_id, $qty)
    → Executes SQL INSERT
    ↓
MySQL Database
    → Inserts record into cart table
    ↓
Returns success/failure through chain
    ↓
JavaScript updates UI (cart count badge)
```

---

## DATABASE SCHEMA

### Core Tables Overview

| Table | Purpose | Key Relationships |
|-------|---------|-------------------|
| `customer` | Users (customers & planners) | Parent to orders, products, consultations |
| `products` | Service packages | FK: user_id, product_cat, product_brand |
| `categories` | Service categories | FK: user_id (planner-specific) |
| `brands` | Service brands/types | FK: user_id (planner-specific) |
| `cart` | Shopping cart items | FK: p_id (product), c_id (customer) |
| `orders` | Completed orders | FK: customer_id, vendor_id |
| `orderdetails` | Order line items | FK: order_id, product_id |
| `payment` | Payment records | FK: customer_id, order_id |
| `wishlist` | Saved products | FK: customer_id, product_id |
| `consultations` | Booking requests | FK: customer_id, planner_id, service_id |
| `consultation_slots` | Planner availability | FK: planner_id |
| `service_types` | Consultation categories | Standalone lookup table |
| `product_package_items` | Package inclusions | FK: product_id |

### Detailed Schema

#### 1. Customer Table
```sql
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL UNIQUE,
  `customer_pass` varchar(150) NOT NULL, -- bcrypt hashed
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(20) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL COMMENT '0=customer, 1=planner/vendor',
  `vendor_name` varchar(100) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `subscription_tier` varchar(20) DEFAULT NULL COMMENT 'starter or premium',
  `subscription_status` varchar(20) DEFAULT 'active',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Key Fields:**
- `user_role`: 0 = Customer (couple), 1 = Planner (vendor)
- `subscription_tier`: 'starter' or 'premium' for planners
- `latitude/longitude`: For location-based search
- `vendor_name`: Business name for planners

#### 2. Products Table
```sql
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_cat` int(11) NOT NULL,
  `product_brand` int(11) NOT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_price` double NOT NULL,
  `product_desc` varchar(500) DEFAULT NULL,
  `product_image` varchar(100) DEFAULT NULL,
  `product_keywords` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Owner/Planner ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  KEY `product_cat` (`product_cat`),
  KEY `product_brand` (`product_brand`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Note:** In VendorConnect, "products" represent service packages offered by planners (e.g., "Gold Wedding Package", "Budget Decor Package").

#### 3. Orders & Order Details
```sql
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `review_comment` text DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `vendor_id` (`vendor_id`)
) ENGINE=InnoDB;

CREATE TABLE `orderdetails` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `selected_items` text DEFAULT NULL COMMENT 'JSON array of package items',
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB;
```

**Invoice Generation:** 9-digit number = last 6 digits of timestamp + 3 random digits

#### 4. Payment Table
```sql
CREATE TABLE `payment` (
  `pay_id` int(11) NOT NULL AUTO_INCREMENT,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT 'direct',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `authorization_code` varchar(100) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'card, mobile_money, bank',
  PRIMARY KEY (`pay_id`),
  KEY `idx_transaction_ref` (`transaction_ref`)
) ENGINE=InnoDB;
```

#### 5. Consultation System
```sql
CREATE TABLE `consultations` (
  `consultation_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `planner_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `consultation_date` date NOT NULL,
  `consultation_time` time NOT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `consultation_fee` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','completed','cancelled','no-show') DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `payment_reference` varchar(100) DEFAULT NULL,
  `customer_notes` text,
  `planner_notes` text,
  `meeting_location` varchar(255) DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`consultation_id`)
) ENGINE=InnoDB;
```

### Entity Relationship Diagram (ERD)

```
customer (1) ──────< (M) products
    │                      │
    │                      │
    │                   orders (M) >───< (M) products
    │                      │              (via orderdetails)
    │                      │
    │                   payment (M)
    │
    ├────< (M) cart >───< (M) products
    │
    ├────< (M) wishlist >───< (M) products
    │
    ├────< (M) categories
    │
    ├────< (M) brands
    │
    └────< (M) consultations >──── (1) service_types
             │
             └────< (M) consultation_slots
```

---

## AUTHENTICATION SYSTEM

### User Roles

| Role | Value | Description | Access |
|------|-------|-------------|--------|
| Customer | 0 or 2 | Couples planning weddings | Browse, Cart, Orders, Consultations |
| Planner/Vendor | 1 | Wedding service providers | Dashboard, Products, Availability |

### Registration Flow

**File:** `actions/register_customer_action.php`

```php
// Registration process
1. Validate input (email, password, phone, name)
2. Check email availability
3. Hash password with password_hash()
4. Capture location (optional)
5. Insert into customer table
6. Return success/error JSON
```

**Validations:**
- Email: Valid format, unique
- Password: Minimum 6 characters
- Name: Letters, spaces, hyphens, apostrophes only
- Phone: International format (+233XXXXXXXXX)
- Role: Must be 1 (planner) or 2 (customer)
- Subscription (planners only): 'starter' or 'premium'

**Code Example:**
```php
// Password hashing
$hashed_password = password_hash($customer_pass, PASSWORD_DEFAULT);

// Email validation
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Invalid email format');
}

// Phone validation (international)
if (!preg_match("/^\+[0-9]{1,4}[0-9]{6,15}$/", $phone_clean)) {
    throw new Exception('Invalid phone number');
}
```

### Login Flow

**File:** `actions/login_action.php`

```php
// Login process
1. Validate email and password presence
2. Retrieve user by email
3. Verify password with password_verify()
4. Create session variables
5. Redirect based on user_role
```

**Session Variables:**
```php
$_SESSION['customer_id']         // User ID
$_SESSION['customer_name']       // Display name
$_SESSION['customer_email']      // Email
$_SESSION['user_role']           // 0/1/2
$_SESSION['subscription_tier']   // starter/premium (planners)
$_SESSION['subscription_status'] // active/expired
```

### Protected Routes

**File:** `settings/core.php`

```php
function check_login() {
    return isset($_SESSION['customer_id']);
}

function get_user_id() {
    return $_SESSION['customer_id'] ?? null;
}

function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

// Usage in protected pages
if (!check_login()) {
    header("Location: ../login/login.php");
    exit();
}
```

### Password Reset

**Files:**
- `actions/forgot_password_action.php` - Generate reset token
- `actions/reset_password_action.php` - Update password

**Process:**
1. User enters email
2. System generates reset token (future: send email)
3. User enters token + new password
4. Password updated with password_hash()

---

## CORE FEATURES

### 1. Product Management (Vendor Dashboard)

**Location:** `admin/product.php`

**Capabilities:**
- ✅ Add new service packages
- ✅ Edit existing packages
- ✅ Delete packages
- ✅ Upload product images
- ✅ Define package items (food, venue, decor, etc.)
- ✅ Set pricing
- ✅ Category and brand assignment

**Add Product Flow:**
```javascript
// admin/product.php
User fills form → JavaScript validates → 
POST to add_product_action.php →
product_controller.php (add_product_ctr) →
product_class.php (add_product method) →
SQL INSERT → Returns product_id
```

**Class Methods:**
```php
// classes/product_class.php
add_product($data)               // Create
get_product($product_id, $user_id) // Read
update_product($data)            // Update
delete_product($product_id, $user_id) // Delete
```

### 2. Product Browsing (Customer View)

**Location:** `view/all_products.php`

**Features:**
- ✅ Grid/list view of all products
- ✅ Real-time search
- ✅ Filter by category
- ✅ Filter by brand
- ✅ Filter by price range (min/max)
- ✅ Sort by: Price (Low→High), Price (High→Low), Newest
- ✅ Location-based sorting (distance from user)
- ✅ Pagination
- ✅ Add to cart
- ✅ Add to wishlist

**Search Implementation:**
```javascript
// js/all_products.js
searchBtn.addEventListener('click', () => {
    const query = searchInput.value.trim();
    if (query) {
        window.location.href = `product_search_result.php?query=${encodeURIComponent(query)}`;
    }
});
```

**Filter API:**
```php
// actions/filter_products_action.php
POST {
    "category": 5,
    "brand": 3,
    "price_min": 1000,
    "price_max": 5000,
    "sort": "price_asc"
}

// Returns JSON array of products
```

### 3. Shopping Cart

**Location:** `view/cart.php`

**Features:**
- ✅ View cart items with images
- ✅ Update quantities (+ / -)
- ✅ Remove items
- ✅ Package item selection (checkboxes)
- ✅ Real-time subtotal calculation
- ✅ Empty cart option
- ✅ Proceed to checkout

**Cart Persistence:**
- **Guest users:** IP address (`$_SERVER['REMOTE_ADDR']`)
- **Logged-in users:** Customer ID

**Cart Operations:**

| Action | Endpoint | Method |
|--------|----------|--------|
| Get cart | `get_cart_action.php` | GET |
| Add item | `add_to_cart_action.php` | POST |
| Update qty | `update_quantity_action.php` | POST |
| Remove item | `remove_from_cart_action.php` | POST |
| Empty cart | `empty_cart_action.php` | POST |

**Code Example:**
```php
// Add to cart
$cart_data = [
    'p_id' => $product_id,
    'ip_add' => $_SERVER['REMOTE_ADDR'],
    'c_id' => $_SESSION['customer_id'] ?? null,
    'qty' => $quantity,
    'selected_items' => json_encode($package_items)
];

$result = add_to_cart_ctr($cart_data);
```

### 4. Checkout & Payment

**Location:** `view/checkout.php`

**Checkout Flow:**
```
Cart → Checkout (review) → Paystack → Payment Callback → Verify → Success
```

**Steps:**

1. **Review Order** (`checkout.php`)
   - Display cart items
   - Show total amount
   - Customer information confirmation

2. **Initialize Payment** (`actions/paystack_init_transaction.php`)
   ```php
   $data = [
       'email' => $customer_email,
       'amount' => $amount_in_pesewas, // GHS * 100
       'reference' => $unique_reference,
       'callback_url' => $callback_url
   ];
   
   $response = paystack_initialize_transaction(...);
   // Returns authorization_url
   ```

3. **Customer Pays** (Paystack hosted page)
   - Redirected to Paystack
   - Enters payment details
   - Paystack processes payment

4. **Callback** (`view/paystack_callback.php`)
   - Receives reference parameter
   - Displays loading screen
   - Calls verification endpoint

5. **Verify Payment** (`actions/paystack_verify_payment.php`)
   ```php
   // Verify with Paystack API
   $verification = paystack_verify_transaction($reference);
   
   if ($verification['data']['status'] === 'success') {
       // Create order
       $invoice_no = generate_invoice_number();
       $order_id = create_order($customer_id, $invoice_no);
       
       // Save order details
       foreach ($cart_items as $item) {
           add_order_detail($order_id, $item);
       }
       
       // Record payment
       add_payment($customer_id, $order_id, $amount);
       
       // Clear cart
       empty_cart($customer_id);
       
       return ['status' => 'success', 'order_id' => $order_id];
   }
   ```

6. **Success Page** (`view/payment_success.php`)
   - Display order confirmation
   - Show invoice number
   - Download invoice option
   - Link to order details

**Payment Methods Supported:**
- 💳 Credit/Debit Cards (Visa, Mastercard, Verve)
- 📱 Mobile Money (MTN, Vodafone, AirtelTigo)
- 🏦 Bank Transfer
- 📞 USSD

### 5. Order Management

**Customer View:** `view/orders.php`

**Features:**
- ✅ View all orders
- ✅ Order details (products, quantities, prices)
- ✅ Payment information
- ✅ Order status tracking
- ✅ Rate and review completed orders
- ✅ Invoice download

**Order Statuses:**
- `Pending` - Order created, payment pending
- `Paid` - Payment confirmed
- `Processing` - Vendor preparing
- `Completed` - Service delivered
- `Cancelled` - Order cancelled

**Rating System:**
```php
// actions/submit_rating_action.php
POST {
    "order_id": 123,
    "rating": 4.5,  // 1-5 stars
    "review_comment": "Excellent service!"
}

// Updates orders table
UPDATE orders 
SET rating = ?, review_comment = ? 
WHERE order_id = ?
```

### 6. Consultation Booking

**Location:** `view/book_consultation.php`

**Process:**

1. **Select Service Type**
   - Initial Consultation
   - Venue Selection
   - Budget Planning
   - Design & Decoration
   - Full Event Planning
   - etc.

2. **Choose Date & Time**
   - Calendar picker
   - Available slots fetched from planner's schedule
   - Duration: 60 minutes (default)

3. **Enter Details**
   - Customer notes
   - Preferred meeting location

4. **Pay Consultation Fee**
   - Paystack integration
   - Fee varies by planner

5. **Confirmation**
   - Booking created with status 'pending'
   - Email notification (future feature)

**Planner Availability Management:**
```php
// admin/availability.php
Planner sets recurring weekly slots:
- Monday 9:00-17:00
- Tuesday 10:00-18:00
- etc.

// consultation_slots table
INSERT INTO consultation_slots (planner_id, day_of_week, start_time, end_time)
VALUES (123, 1, '09:00:00', '17:00:00'); // Monday
```

**Get Available Slots:**
```php
// actions/get_available_slots_action.php
GET ?planner_id=123&date=2025-12-01

// Returns slots not already booked
SELECT * FROM consultation_slots 
WHERE planner_id = 123 
  AND day_of_week = DAYOFWEEK('2025-12-01')
  AND is_available = 1
  AND slot_id NOT IN (
      SELECT slot_id FROM consultations 
      WHERE consultation_date = '2025-12-01'
  )
```

### 7. Wishlist

**Location:** `view/wishlist.php`

**Features:**
- ✅ Add products to wishlist
- ✅ Remove from wishlist
- ✅ View all wishlist items
- ✅ Quick add to cart from wishlist

**Endpoints:**
- `actions/add_to_wishlist_action.php`
- `actions/remove_from_wishlist_action.php`
- `actions/get_wishlist_action.php`

### 8. Vendor Profile

**Location:** `view/vendor_profile.php?vendor_id=123`

**Public Profile Contains:**
- ✅ Vendor name and photo
- ✅ About section
- ✅ Service packages (products)
- ✅ Reviews and ratings
- ✅ Average rating (stars)
- ✅ Contact information
- ✅ Book consultation button

**Code Example:**
```php
// actions/get_vendor_profile_action.php
GET ?vendor_id=123

// Returns
{
    "vendor": {
        "customer_id": 123,
        "vendor_name": "Elegant Events Ghana",
        "about": "Premier wedding planning...",
        "profile_picture": "uploads/vendor123.jpg",
        "average_rating": 4.8
    },
    "products": [...],
    "reviews": [...]
}
```

---

## API ENDPOINTS

### Authentication

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/register_customer_action.php` | POST | Register new user | name, email, password, country, city, contact, user_role |
| `/actions/login_action.php` | POST | User login | email, password |
| `/actions/check_email_availability.php` | GET | Check if email exists | email |
| `/actions/forgot_password_action.php` | POST | Request password reset | email |
| `/actions/reset_password_action.php` | POST | Reset password | token, new_password |

### Products

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/view_all_products_action.php` | GET | Get all products | limit, offset, user_lat, user_lon |
| `/actions/search_products_action.php` | GET | Search products | query, limit, offset |
| `/actions/filter_products_action.php` | POST | Filter products | category, brand, price_min, price_max, sort |
| `/actions/get_filter_options_action.php` | GET | Get filter dropdowns | - |
| `/actions/add_product_action.php` | POST | Add product (vendor) | title, price, desc, cat, brand, image |
| `/actions/update_product_action.php` | POST | Update product | product_id, title, price, desc |
| `/actions/delete_product_action.php` | POST | Delete product | product_id |
| `/actions/fetch_product_action.php` | GET | Get single product | product_id |

### Cart

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/get_cart_action.php` | GET | Get cart items | - |
| `/actions/add_to_cart_action.php` | POST | Add to cart | product_id, qty, selected_items |
| `/actions/update_quantity_action.php` | POST | Update quantity | product_id, qty |
| `/actions/remove_from_cart_action.php` | POST | Remove from cart | product_id |
| `/actions/empty_cart_action.php` | POST | Clear cart | - |

### Orders

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/get_user_orders_action.php` | GET | Get user orders | customer_id |
| `/actions/get_order_details_action.php` | GET | Get order details | order_id |
| `/actions/submit_rating_action.php` | POST | Rate order | order_id, rating, comment |

### Payment

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/paystack_init_transaction.php` | POST | Initialize payment | email, amount, cart_items |
| `/actions/paystack_verify_payment.php` | POST | Verify payment | reference, cart_items, total_amount |
| `/actions/subscription_verify_payment.php` | POST | Verify subscription | reference |

### Consultations

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/create_consultation_action.php` | POST | Book consultation | planner_id, service_id, date, time, fee |
| `/actions/get_available_slots_action.php` | GET | Get planner slots | planner_id, date |
| `/actions/get_customer_consultations_action.php` | GET | Customer bookings | customer_id |
| `/actions/get_planner_consultations_action.php` | GET | Planner bookings | planner_id |
| `/actions/update_consultation_status_action.php` | POST | Update status | consultation_id, status |
| `/actions/verify_consultation_payment_action.php` | POST | Verify payment | reference, consultation_id |

### Wishlist

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/get_wishlist_action.php` | GET | Get wishlist | customer_id |
| `/actions/add_to_wishlist_action.php` | POST | Add to wishlist | product_id |
| `/actions/remove_from_wishlist_action.php` | POST | Remove from wishlist | product_id |

### Admin/Vendor

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/actions/add_category_action.php` | POST | Add category | cat_name |
| `/actions/add_brand_action.php` | POST | Add brand | brand_name |
| `/actions/add_time_slot_action.php` | POST | Add availability | day_of_week, start_time, end_time |
| `/actions/get_planner_analytics_action.php` | GET | Get stats | planner_id |
| `/actions/get_planner_sales_action.php` | GET | Sales data | planner_id |

---

## PAYMENT INTEGRATION

### Paystack Configuration

**File:** `settings/paystack_config.php`

```php
// Paystack API Configuration
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxx');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxxx');
define('PAYSTACK_BASE_URL', 'https://api.paystack.co');

// Helper functions
function ghs_to_pesewas($amount) {
    return intval($amount * 100);
}

function pesewas_to_ghs($pesewas) {
    return floatval($pesewas / 100);
}
```

### Initialize Transaction

```php
function paystack_initialize_transaction($email, $amount, $reference, $callback_url) {
    $url = PAYSTACK_BASE_URL . "/transaction/initialize";
    
    $fields = [
        'email' => $email,
        'amount' => $amount, // in pesewas
        'reference' => $reference,
        'callback_url' => $callback_url,
        'currency' => 'GHS',
        'channels' => ['card', 'bank', 'ussd', 'mobile_money']
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

### Verify Transaction

```php
function paystack_verify_transaction($reference) {
    $url = PAYSTACK_BASE_URL . "/transaction/verify/" . $reference;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ['status' => false, 'message' => 'Connection error'];
    }
    
    curl_close($ch);
    
    return json_decode($response, true);
}
```

### Payment Response Structure

**Successful Payment:**
```json
{
    "status": true,
    "message": "Verification successful",
    "data": {
        "id": 1234567890,
        "reference": "ref_abc123xyz",
        "amount": 500000,
        "currency": "GHS",
        "status": "success",
        "paid_at": "2025-11-29T10:30:00.000Z",
        "customer": {
            "email": "customer@example.com"
        },
        "authorization": {
            "authorization_code": "AUTH_abc123",
            "card_type": "visa",
            "last4": "1234",
            "bank": "Access Bank"
        },
        "channel": "card"
    }
}
```

### Error Handling

```php
try {
    $verification = paystack_verify_transaction($reference);
    
    if (!$verification || !isset($verification['status'])) {
        throw new Exception("No response from Paystack");
    }
    
    if ($verification['status'] !== true) {
        throw new Exception($verification['message'] ?? 'Verification failed');
    }
    
    if ($verification['data']['status'] !== 'success') {
        throw new Exception("Payment not successful");
    }
    
    // Process order
    
} catch (Exception $e) {
    error_log("Payment error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
```

---

## FILE STRUCTURE

```
/Applications/MAMP/htdocs/Authenticate-Activity-1/
│
├── index.php                    # Homepage
│
├── actions/                     # API endpoints (50+ files)
│   ├── add_to_cart_action.php
│   ├── login_action.php
│   ├── paystack_verify_payment.php
│   └── ...
│
├── admin/                       # Vendor dashboard
│   ├── dashboard.php           # My products
│   ├── product.php             # Add/edit product
│   ├── category.php            # Manage categories
│   ├── brand.php               # Manage brands
│   ├── availability.php        # Set time slots
│   ├── consultations.php       # Booking management
│   └── subscription_payment.php
│
├── classes/                     # Business logic (10 files)
│   ├── customer_class.php      # User management
│   ├── product_class.php       # Product CRUD
│   ├── cart_class.php          # Cart operations
│   ├── order_class.php         # Order management
│   ├── consultation_class.php  # Booking system
│   ├── wishlist_class.php
│   ├── category_class.php
│   ├── brand_class.php
│   ├── location_class.php
│   └── user_class.php
│
├── controllers/                 # Request handlers (9 files)
│   ├── customer_controller.php
│   ├── product_controller.php
│   ├── cart_controller.php
│   ├── order_controller.php
│   ├── consultation_controller.php
│   └── ...
│
├── css/                        # Stylesheets (17 files)
│   ├── index.css
│   ├── all_products.css
│   ├── cart.css
│   ├── checkout.css
│   ├── dashboard.css
│   └── ...
│
├── db/
│   └── dbforlab.sql           # Complete database schema
│
├── js/                         # JavaScript files
│   ├── all_products.js
│   ├── cart.js
│   ├── checkout.js
│   └── ...
│
├── login/                      # Authentication pages
│   ├── login.php
│   ├── register.php
│   ├── select_role.php
│   ├── select_subscription.php
│   ├── logout.php
│   └── ...
│
├── settings/                   # Configuration
│   ├── db_class.php           # Database connection class
│   ├── db_cred.php            # Database credentials
│   ├── core.php               # Session management
│   └── paystack_config.php    # Payment configuration
│
├── uploads/                    # User-uploaded files
│   ├── products/              # Product images
│   └── profiles/              # Profile pictures
│
└── view/                       # Customer pages (15 files)
    ├── all_products.php       # Product listing
    ├── single_product.php     # Product details
    ├── product_search_result.php
    ├── cart.php               # Shopping cart
    ├── checkout.php           # Checkout page
    ├── paystack_callback.php  # Payment callback
    ├── payment_success.php    # Success page
    ├── orders.php             # Order history
    ├── book_consultation.php  # Booking page
    ├── my_consultations.php   # Customer bookings
    ├── vendor_profile.php     # Public vendor page
    ├── wishlist.php           # Wishlist
    └── ...
```

---

## SETUP INSTRUCTIONS

### Prerequisites

- MAMP/XAMPP (PHP 7.4+, MySQL 5.7+)
- Web browser
- Paystack account (for payments)
- Internet connection

### Installation Steps

1. **Clone Repository**
   ```bash
   cd /Applications/MAMP/htdocs/
   git clone https://github.com/Nanaafiaasante/Authenticate-Activity-1.git
   cd Authenticate-Activity-1
   ```

2. **Start MAMP**
   - Open MAMP application
   - Start servers (Apache & MySQL)
   - Ensure ports: Apache (80), MySQL (3306)

3. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpMyAdmin`
   - Click "New" to create database
   - Name: `ecommerce_2025A_nana_asante`
   - Collation: `utf8mb4_general_ci`

4. **Import Schema**
   - Select the database
   - Click "Import" tab
   - Choose file: `db/dbforlab.sql`
   - Click "Go"
   - Verify 15 tables created

5. **Configure Database Connection**
   
   Edit `settings/db_cred.php`:
   ```php
   <?php
   define('SERVER', 'localhost');
   define('USERNAME', 'root');
   define('PASSWD', 'root'); // Change if different
   define('DATABASE', 'ecommerce_2025A_nana_asante');
   ?>
   ```

6. **Configure Paystack**
   
   Edit `settings/paystack_config.php`:
   ```php
   define('PAYSTACK_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY');
   define('PAYSTACK_PUBLIC_KEY', 'pk_test_YOUR_PUBLIC_KEY');
   ```
   
   Get keys from: https://dashboard.paystack.com/settings/developer

7. **Set Permissions**
   ```bash
   chmod -R 755 uploads/
   chmod -R 755 uploads/products/
   chmod -R 755 uploads/profiles/
   ```

8. **Access Application**
   - Homepage: `http://localhost/Authenticate-Activity-1/`
   - Login: `http://localhost/Authenticate-Activity-1/login/login.php`
   - Register: `http://localhost/Authenticate-Activity-1/login/select_role.php`


### Paystack Test Cards

**Successful Transaction:**
- Card: 4084084084084081
- CVV: 408
- Expiry: 12/30
- PIN: 0000
- OTP: 123456

**Declined Transaction:**
- Card: 5060990580000217634
- CVV: 123
- Expiry: 12/30

---

## SECURITY FEATURES

### 1. Password Security

✅ **Bcrypt Hashing**
```php
// Registration
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Login verification
if (password_verify($input_password, $stored_hash)) {
    // Authenticated
}
```

### 2. SQL Injection Prevention

✅ **Prepared Statements** (customer_class.php)
```php
$stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
$stmt->bind_param("s", $customer_email);
$stmt->execute();
```

⚠️ **Escaped Strings** (other classes)
```php
$product_title = mysqli_real_escape_string($conn, $data['product_title']);
$sql = "INSERT INTO products (product_title) VALUES ('$product_title')";
```

**Recommendation:** Migrate all to prepared statements.

### 3. XSS Prevention

✅ **Output Escaping**
```php
echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8');
```

### 4. Session Management

✅ **Session Security**
```php
// Start session
session_start();

// Regenerate on login
session_regenerate_id(true);

// Destroy on logout
session_destroy();
```

### 5. Input Validation

✅ **Server-Side Validation**
- Email format
- Password strength
- Phone number format
- Data type checking
- Required field validation

### 6. HTTPS (Production)

⚠️ **TODO:** Force HTTPS in production
```php
// .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 7. File Upload Security

✅ **Image Upload Validation**
```php
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    throw new Exception('Invalid file type');
}

// Rename file
$new_name = uniqid() . '.' . $ext;
move_uploaded_file($_FILES['file']['tmp_name'], "uploads/products/$new_name");
```

### 8. CSRF Protection

```php
// Generate
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Verify
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Invalid token');
}
```

---

## ADDITIONAL NOTES

### Browser Compatibility
- Chrome 90+ ✅
- Firefox 88+ ✅
- Safari 14+ ✅
- Edge 90+ ✅

### Mobile Responsive
- Viewport meta tag present
- Bootstrap grid system
- Touch-friendly buttons
- Mobile-optimized forms

### Performance Considerations
- Database indexes on foreign keys
- AJAX for async operations
- Image optimization recommended
- CDN for Bootstrap/icons

### Future Enhancements
1. **AI Recommendations** - Suggest products based on browsing
2. **Email Notifications** - Order confirmations, booking reminders
3. **SMS Notifications** - Via Twilio/Africa's Talking
4. **Admin Panel** - Super admin for platform management
5. **Analytics Dashboard** - Google Analytics integration
6. **SEO Optimization** - Meta tags, sitemaps
7. **Multi-language** - English, Twi, French
8. **Progressive Web App** - Offline capability
9. **Social Login** - Google, Facebook OAuth
10. **Real-time Chat** - Vendor-customer messaging

---

## SUPPORT & CONTACT

**Developer:** Nana Afia Asante  
**Course:** CS442 – Electronic Commerce  
**Institution:** Ashesi University  
**Date:** November 2025

**Repository:** https://github.com/Nanaafiaasante/Authenticate-Activity-1

---

**End of Technical Documentation**
