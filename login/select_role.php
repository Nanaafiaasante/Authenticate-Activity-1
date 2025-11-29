<?php
/**
 * Role Selection Page
 * Users select whether they are a Couple (Customer) or Planner (Vendor)
 */
session_start();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Role - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/select_role.css">
</head>
<body>
    <!-- VENDORCONNECT GHANA LOGO -->
    <a href="../index.php" class="vc-logo">
        <div class="vc-logo-ring"></div>
        <div class="vc-logo-text">
            <div class="vc-logo-main">VendorConnect</div>
            <div class="vc-logo-sub">GHANA</div>
        </div>
    </a>

    <!-- DECORATIVE ELEMENTS -->
    <div class="botanical-tl"></div>
    <div class="botanical-tr"></div>
    <div class="botanical-bl"></div>
    <div class="botanical-br"></div>

    <div class="gold-frame-tr"></div>
    <div class="gold-frame-bl"></div>

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

    <!-- Main Content -->
    <div class="container role-container">
        <div class="text-center mb-5">
            <h1 class="page-title">Welcome to VendorConnect Ghana</h1>
            <p class="page-subtitle">Choose how you'd like to join our community</p>
        </div>

        <div class="row justify-content-center g-4">
            <!-- Couple Card -->
            <div class="col-lg-5 col-md-6">
                <div class="role-card couple-card">
                    <div class="role-icon">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h2 class="role-title">I'm a Couple</h2>
                    <p class="role-description">
                        Planning your special day? Browse and book amazing wedding vendors, 
                        manage your cart, and bring your dream wedding to life.
                    </p>
                    <ul class="role-features">
                        <li><i class="bi bi-check-circle-fill"></i> Browse all vendors and services</li>
                        <li><i class="bi bi-check-circle-fill"></i> Add items to cart</li>
                        <li><i class="bi bi-check-circle-fill"></i> Secure checkout & payment</li>
                        <li><i class="bi bi-check-circle-fill"></i> Track your orders</li>
                        <li><i class="bi bi-check-circle-fill"></i> Save favorites</li>
                    </ul>
                    <a href="register.php?role=couple" class="btn-role-select">
                        Continue as Couple
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>

            <!-- Planner/Vendor Card -->
            <div class="col-lg-5 col-md-6">
                <div class="role-card planner-card">
                    <div class="role-icon">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <h2 class="role-title">I'm a Planner</h2>
                    <p class="role-description">
                        Ready to showcase your services? Create listings, manage your products, 
                        and connect with couples planning their perfect wedding.
                    </p>
                    <ul class="role-features">
                        <li><i class="bi bi-check-circle-fill"></i> Create & manage products</li>
                        <li><i class="bi bi-check-circle-fill"></i> Dashboard analytics</li>
                        <li><i class="bi bi-check-circle-fill"></i> Manage categories & brands</li>
                        <li><i class="bi bi-check-circle-fill"></i> Receive & track orders</li>
                        <li><i class="bi bi-check-circle-fill"></i> Build your portfolio</li>
                    </ul>
                                            <a href="select_subscription.php?role=planner" class="btn-role-select">
                            Continue as Planner <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <p class="login-link">
                Already have an account? 
                <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
