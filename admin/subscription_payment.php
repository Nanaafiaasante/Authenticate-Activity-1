<?php
/**
 * Subscription Payment Page
 * Allows planners to complete their subscription payment
 */

require_once '../settings/core.php';

// Check if user is logged in and is a planner
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}

// Get tier and amount from URL
$tier = isset($_GET['tier']) ? $_GET['tier'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';

if (empty($tier) || empty($amount)) {
    header("Location: dashboard.php");
    exit;
}

$tier_name = ($tier === 'premium') ? 'Premium' : 'Basic';
$customer_email = $_SESSION['customer_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Payment - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafb 0%, #f1f5f9 100%);
        }
        
        .payment-card {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-header h2 {
            font-family: 'Playfair Display', serif;
            color: #2c2c2c;
            margin-bottom: 10px;
        }
        
        .price-display {
            font-size: 3rem;
            font-weight: 700;
            color: #C9A961;
            margin: 20px 0;
        }
        
        .plan-features {
            background: #f8fafb;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }
        
        .btn-pay {
            background: linear-gradient(135deg, #1e4d2b, #2d5a3a);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-pay:hover {
            background: linear-gradient(135deg, #0f261a, #1e4d2b);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="header-container">
                <!-- Left: Logo -->
                <div class="header-left">
                    <a href="../index.php" class="vc-logo">
                        <div class="vc-logo-ring"></div>
                        <div class="vc-logo-text">
                            <div class="vc-logo-main">VendorConnect</div>
                            <div class="vc-logo-sub">GHANA</div>
                        </div>
                    </a>
                </div>
                
                <!-- Center: Page Title -->
                <div class="header-center">
                    <h1 class="page-title">Complete Payment</h1>
                    <p class="page-subtitle">Activate your subscription</p>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="dashboard.php" class="header-nav-btn">
                        <i class="bi bi-arrow-left"></i>
                        <span class="nav-label">Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="payment-card">
            <div class="payment-header">
                <h2><i class="bi bi-gem me-2"></i><?php echo $tier_name; ?> Plan</h2>
                <div class="price-display">
                    GHS <?php echo $amount; ?>
                    <small style="font-size: 1.2rem; color: #666;">/month</small>
                </div>
            </div>

            <div class="plan-features">
                <h5 class="mb-3"><i class="bi bi-check-circle me-2" style="color: #1e4d2b;"></i>What's Included:</h5>
                <?php if ($tier === 'premium'): ?>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Unlimited Products</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Advanced Analytics Suite</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Featured Search Placement</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Priority Support (4hr response)</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Marketing Automation Tools</span>
                    </div>
                <?php else: ?>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Up to 25 Products</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Basic Analytics Dashboard</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>5 Portfolio Galleries</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Standard Support</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check2" style="color: #1e4d2b; font-size: 1.2rem;"></i>
                        <span>Client Inquiry Management</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <small>Payment will be processed securely via Paystack. You can cancel anytime.</small>
            </div>

            <form id="paymentForm" action="../actions/subscription_verify_payment.php" method="POST">
                <input type="hidden" name="tier" value="<?php echo htmlspecialchars($tier); ?>">
                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($amount); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($customer_email); ?>">
                
                <button type="submit" class="btn-pay">
                    <i class="bi bi-credit-card me-2"></i>
                    Pay with Paystack
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>
                    I'll pay later
                </a>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const amount = parseInt(form.amount.value) * 100; // Convert to kobo
            const email = form.email.value;
            const tier = form.tier.value;
            
            const handler = PaystackPop.setup({
                key: 'pk_test_0126e6c3ee1d860dcd63402585d54e8fae03e3fe',
                email: email,
                amount: amount,
                currency: 'GHS',
                ref: 'SUB_' + Math.floor((Math.random() * 1000000000) + 1),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Subscription Tier",
                            variable_name: "tier",
                            value: tier
                        }
                    ]
                },
                callback: function(response) {
                    // Payment successful - verify with backend
                    fetch('../actions/subscription_verify_payment.php?reference=' + response.reference + '&tier=' + tier)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Payment successful! Your subscription has been activated.');
                                window.location.href = 'dashboard.php';
                            } else {
                                alert('Payment verification failed: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Verification error:', error);
                            alert('Error verifying payment. Please contact support with reference: ' + response.reference);
                        });
                },
                onClose: function() {
                    alert('Payment window closed.');
                }
            });
            
            handler.openIframe();
        });
    </script>
</body>
</html>
