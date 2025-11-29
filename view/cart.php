<?php
/**
 * Shopping Cart Page
 * Displays cart items and allows users to manage their cart
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>

    <!-- EMERALD GREEN BOTANICALS in all 4 corners -->
    <div class="botanical-tl"></div>
    <div class="botanical-tr"></div>
    <div class="botanical-bl"></div>
    <div class="botanical-br"></div>

    <!-- GOLD RECTANGULAR FRAMES -->
    <div class="gold-frame-tr"></div>
    <div class="gold-frame-bl"></div>

    <!-- SHINY GOLD DOTS scattered -->
    <div class="gold-dot dot-tr1"></div>
    <div class="gold-dot dot-tr2"></div>
    <div class="gold-dot dot-tr3"></div>
    <div class="gold-dot dot-tr4"></div>
    <div class="gold-dot dot-tr5"></div>
    <div class="gold-dot dot-tr6"></div>
    <div class="gold-dot dot-tr7"></div>

    <div class="gold-dot dot-bl1"></div>
    <div class="gold-dot dot-bl2"></div>
    <div class="gold-dot dot-bl3"></div>
    <div class="gold-dot dot-bl4"></div>
    <div class="gold-dot dot-bl5"></div>
    <div class="gold-dot dot-bl6"></div>
    <div class="gold-dot dot-bl7"></div>
    
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <!-- Logo -->
            <div class="header-left">
                <a href="../index.php" class="vc-logo">
                    <div class="vc-logo-ring"></div>
                    <div class="vc-logo-text">
                        <div class="vc-logo-main">VendorConnect</div>
                        <div class="vc-logo-sub">GHANA</div>
                    </div>
                </a>
            </div>
            
            <!-- Center - Title -->
            <div class="header-center">
                <h1 class="page-title"><i class="bi bi-cart3 me-2"></i>Shopping Cart</h1>
            </div>
            
            <!-- Navigation -->
            <div class="header-right">
                <a href="all_products.php" class="btn-header-nav">
                    <i class="bi bi-arrow-left"></i>
                    <span class="btn-nav-label">Continue Shopping</span>
                </a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                    <a href="../admin/dashboard.php" class="btn-header-nav">
                        <i class="bi bi-grid"></i>
                        <span class="btn-nav-label">Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="../index.php" class="btn-header-nav">
                        <i class="bi bi-house"></i>
                        <span class="btn-nav-label">Home</span>
                    </a>
                <?php endif; ?>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="../login/logout.php" class="btn-header-nav btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="btn-nav-label">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="../login/login.php" class="btn-header-nav">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="btn-nav-label">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cart Content -->
    <div class="container cart-container">
        <div class="row">
            <!-- Cart Items Section -->
            <div class="col-lg-8">
                <div class="cart-items-section">
                    <h3 class="mb-4">
                        <i class="bi bi-bag-check me-2"></i>Cart Items
                    </h3>
                    <div id="cartItemsContainer">
                        <!-- Cart items will be loaded here by JavaScript -->
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading your cart...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary Section -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h3><i class="bi bi-receipt me-2"></i>Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">GHS 0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span class="text-success">FREE</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax:</span>
                        <span>GHS 0.00</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="cartTotal">GHS 0.00</span>
                    </div>
                    
                    <div class="cart-actions">
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="checkout.php" class="btn-checkout" id="checkoutBtn">
                                <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                            </a>
                        <?php else: ?>
                            <a href="../login/login.php" class="btn-checkout">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Checkout
                            </a>
                        <?php endif; ?>
                        
                        <a href="all_products.php" class="btn-continue-shopping">
                            <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                        </a>
                        
                        <button class="btn-empty-cart" id="emptyCartBtn" onclick="emptyCart()">
                            <i class="bi bi-trash me-2"></i>Empty Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container for Messages -->
    <div id="alertContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/cart.js"></script>
</body>
</html>
