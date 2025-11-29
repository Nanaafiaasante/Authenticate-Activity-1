<?php
/**
 * Wishlist Page
 * Displays user's saved/wishlist items
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/wishlist.css">
</head>
<body>

    <!-- Header Section -->
    <header class="header-section">
        <div class="container">
            <div class="header-left">
                <a href="../index.php" class="vc-logo">
                    <div class="vc-logo-ring"></div>
                    <div class="vc-logo-text">
                        <div class="vc-logo-main">VendorConnect</div>
                        <div class="vc-logo-sub">GHANA</div>
                    </div>
                </a>
            </div>
            
            <div class="header-center">
                <h1 class="page-title"><i class="bi bi-heart-fill me-2"></i>My Wishlist</h1>
            </div>
            
            <div class="header-right">
                <a href="all_products.php" class="btn-header-nav">
                    <i class="bi bi-arrow-left"></i>
                    <span class="btn-nav-label">Continue Shopping</span>
                </a>
                <a href="cart.php" class="btn-header-nav">
                    <i class="bi bi-cart3"></i>
                    <span class="btn-nav-label">Cart</span>
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
                <a href="../login/logout.php" class="btn-header-nav btn-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="btn-nav-label">Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Wishlist Content -->
    <div class="container wishlist-container">
        <div id="wishlistItemsContainer">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading your wishlist...</p>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/wishlist.js"></script>
</body>
</html>
