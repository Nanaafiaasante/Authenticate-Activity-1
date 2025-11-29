<?php
/**
 * Single Product Page
 * Detailed view of a single product
 */

session_start();

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    header('Location: all_products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/single_product.css">
</head>
<body>
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
            
            <!-- Center - Breadcrumb -->
            <div class="header-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="all_products.php">Products</a></li>
                        <li class="breadcrumb-item active" aria-current="page" id="productBreadcrumb">Product</li>
                    </ol>
                </nav>
            </div>
            
            <!-- Navigation -->
            <div class="header-right">
                <a href="cart.php" class="btn-header-nav">
                    <span class="cart-icon-wrapper">
                        <i class="bi bi-cart3"></i>
                        <span class="cart-count-badge">0</span>
                    </span>
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

    <!-- Main Content -->
    <div class="main-wrapper">
        <div class="container">
            <div id="productContainer">
                <div class="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading product details...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Profile Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e4d2b 0%, #2d5a3a 100%); color: white; border: none;">
                    <h5 class="modal-title" id="vendorModalLabel">
                        <i class="bi bi-shop me-2"></i>Vendor Profile
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="vendorModalBody">
                    <div class="text-center p-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const productId = <?php echo $product_id; ?>;
    </script>
    <script src="../js/cart.js"></script>
    <script src="../js/single_product.js"></script>
</body>
</html>