<?php
/**
 * Checkout Page
 * Handles the checkout process with simulated payment
 */

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: ../login/login.php?redirect=checkout');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/checkout.css">
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
            
            <!-- Center - Title -->
            <div class="header-center">
                <h1 class="page-title"><i class="bi bi-credit-card me-2"></i>Checkout</h1>
            </div>
            
            <!-- Navigation -->
            <div class="header-right">
                <a href="cart.php" class="btn-header-nav">
                    <i class="bi bi-arrow-left"></i>
                    <span class="btn-nav-label">Back to Cart</span>
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

    <!-- Progress Steps -->
    <div class="container">
        <ul class="checkout-progress">
            <li class="progress-step completed">
                <div class="step-circle">
                    <i class="bi bi-check"></i>
                </div>
                <span class="step-label">Cart</span>
            </li>
            <div class="step-line"></div>
            <li class="progress-step active">
                <div class="step-circle">2</div>
                <span class="step-label">Review Order</span>
            </li>
            <div class="step-line"></div>
            <li class="progress-step">
                <div class="step-circle">3</div>
                <span class="step-label">Payment</span>
            </li>
            <div class="step-line"></div>
            <li class="progress-step">
                <div class="step-circle">4</div>
                <span class="step-label">Confirmation</span>
            </li>
        </ul>
    </div>

    <!-- Checkout Content -->
    <div class="container checkout-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="checkout-content">
                    <h3 class="mb-4">
                        <i class="bi bi-receipt me-2"></i>Order Summary
                    </h3>
                    
                    <!-- Checkout Summary Container -->
                    <div id="checkoutSummary">
                        <!-- Items will be loaded here by JavaScript -->
                        <div class="loading-spinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading order summary...</p>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="customer-info mt-4 p-3" style="background: rgba(201, 169, 97, 0.05); border-radius: 10px;">
                        <h5 class="mb-3" style="color: var(--emerald-dark);">
                            <i class="bi bi-person-circle me-2"></i>Customer Information
                        </h5>
                        <p class="mb-1">
                            <strong>Name:</strong> <?php echo isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) : 'N/A'; ?>
                        </p>
                        <p class="mb-1">
                            <strong>Email:</strong> <?php echo isset($_SESSION['customer_email']) ? htmlspecialchars($_SESSION['customer_email']) : 'N/A'; ?>
                        </p>
                    </div>
                    
                    <!-- Payment Section -->
                    <div class="payment-section">
                        <button class="btn-proceed-payment" id="proceedToPaymentBtn" disabled>
                            <i class="bi bi-wallet2 me-2"></i>Proceed to Payment
                        </button>
                        
                        <p class="text-muted mt-3">
                            <i class="bi bi-shield-lock me-2"></i>
                            All transactions are secure and encrypted
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Simulation Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="bi bi-credit-card-2-front me-2"></i> Simulate Payment Confirmation
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="payment-info">
                        <i class="bi bi-cash-coin" style="font-size: 3rem; color: var(--gold-dark);"></i>
                        <div class="payment-amount">
                            <span class="currency">GHS</span>
                            <span id="paymentModalTotal">0.00</span>
                        </div>
                        
                        <p class="mt-4" style="color: var(--text-dark); font-size: 1.1rem;">
                            Please confirm your payment to complete this order.
                        </p>
                        
                        <div class="payment-methods mt-3 p-3" style="background: rgba(201, 169, 97, 0.1); border-radius: 10px;">
                            <p class="mb-2" style="font-size: 0.95rem; color: var(--text-medium);">
                                <i class="bi bi-credit-card me-2"></i>Accepted payment methods:
                            </p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <span class="badge bg-primary">Mobile Money</span>
                                <span class="badge bg-success">Credit Card</span>
                                <span class="badge bg-info">Debit Card</span>
                                <span class="badge bg-warning text-dark">Bank Transfer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel-payment" id="cancelPaymentBtn">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-confirm-payment" id="confirmPaymentBtn">
                        <i class="bi bi-check-circle me-2"></i>Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="checkoutAlertContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/cart.js"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>
