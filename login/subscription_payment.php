<?php
/**
 * Subscription Payment Page
 * Handles payment for planner subscription before registration
 */

// Start session to store payment data
session_start();

// Validate subscription tier and amount from URL
if (!isset($_GET['tier']) || !isset($_GET['amount'])) {
    header('Location: select_subscription.php?role=planner');
    exit();
}

$tier = $_GET['tier'];
$amount = floatval($_GET['amount']);

// Validate tier
$valid_tiers = ['starter', 'premium'];
if (!in_array($tier, $valid_tiers)) {
    header('Location: select_subscription.php?role=planner');
    exit();
}

// Tier details (prices in GHS - Ghana Cedis)
$tier_info = [
    'starter' => [
        'name' => 'Essential Planner',
        'price' => 1500, // GHS 1,500/month (~$99 USD)
        'features' => ['Up to 25 Products', 'Basic Analytics', '5 Portfolio Galleries', 'Standard Support']
    ],
    'premium' => [
        'name' => 'Professional Planner',
        'price' => 3000, // GHS 3,000/month (~$199 USD)
        'features' => ['Unlimited Products', 'Advanced Analytics', 'Unlimited Galleries', 'Priority Support', 'Featured Placement']
    ]
];

$selected_tier = $tier_info[$tier];

// Load Paystack configuration
require_once '../settings/paystack_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Subscription Payment - VendorConnect Ghana</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    
    <style>
        body {
            background: var(--cream);
            font-family: 'Inter', sans-serif;
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }
        
        .step {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .step.completed {
            background: rgba(255,255,255,0.3);
        }
        
        .step.active {
            background: white;
            color: var(--emerald);
        }
        
        .payment-container {
            max-width: 700px;
            margin: -2rem auto 4rem;
            padding: 0 1rem;
        }
        
        .payment-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .plan-summary {
            background: var(--emerald-light);
            padding: 2rem;
            border-bottom: 3px solid var(--emerald);
        }
        
        .plan-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--emerald-dark);
            margin-bottom: 0.5rem;
        }
        
        .plan-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--emerald-dark);
        }
        
        .plan-price .currency {
            font-size: 1.5rem;
        }
        
        .plan-price .period {
            font-size: 1rem;
            font-weight: 400;
            color: var(--gray-medium);
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }
        
        .features-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .features-list i {
            color: var(--emerald);
            font-size: 1.1rem;
        }
        
        .payment-form {
            padding: 2rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 1rem;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid var(--emerald);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .info-box i {
            color: var(--emerald);
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        
        .btn-pay {
            background: var(--emerald);
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-pay:hover {
            background: var(--emerald-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: var(--gray-medium);
            font-size: 0.9rem;
        }
        
        .secure-badge i {
            color: var(--emerald);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="payment-header">
        <div class="container">
            <h1>Complete Your Subscription</h1>
            <p class="mb-0">Secure payment to activate your planner account</p>
            <div class="step-indicator">
                <span class="step completed">1. Role</span>
                <span class="step-divider text-white">→</span>
                <span class="step completed">2. Subscription</span>
                <span class="step-divider text-white">→</span>
                <span class="step active">3. Payment</span>
                <span class="step-divider text-white">→</span>
                <span class="step">4. Register</span>
            </div>
        </div>
    </div>

    <!-- Payment Container -->
    <div class="payment-container">
        <div class="payment-card">
            <!-- Plan Summary -->
            <div class="plan-summary">
                <div class="plan-name"><?php echo htmlspecialchars($selected_tier['name']); ?></div>
                <div class="plan-price">
                    <span class="currency">GHS</span> <?php echo number_format($selected_tier['price'], 0); ?>
                    <span class="period">/month</span>
                </div>
                
                <ul class="features-list">
                    <?php foreach (array_slice($selected_tier['features'], 0, 4) as $feature): ?>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Payment Form -->
            <div class="payment-form">
                <div class="info-box">
                    <i class="bi bi-info-circle"></i>
                    <strong>Next Step:</strong> After successful payment, you'll be directed to complete your registration form.
                </div>

                <form id="paymentForm">
                    <div class="form-section">
                        <label for="email" class="form-label section-title">
                            <i class="bi bi-envelope me-2"></i>Email Address
                        </label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required 
                               placeholder="your.email@example.com">
                        <small class="form-text text-muted">Payment receipt will be sent to this email</small>
                    </div>

                    <div class="form-section">
                        <label for="name" class="form-label section-title">
                            <i class="bi bi-person me-2"></i>Full Name
                        </label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name" required 
                               placeholder="Your full name">
                    </div>

                    <button type="submit" class="btn btn-pay" id="payBtn">
                        <i class="bi bi-shield-lock me-2"></i>
                        Pay GHS <?php echo number_format($selected_tier['price'], 0); ?> with Paystack
                    </button>

                    <div class="secure-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>Secure payment powered by Paystack</span>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="select_subscription.php?role=planner" class="text-muted">
                        <i class="bi bi-arrow-left me-1"></i>Change subscription plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Paystack Inline JS -->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const tier = <?php echo json_encode($tier); ?>;
        const amount = <?php echo $selected_tier['price']; ?>;
        const paystackPublicKey = <?php echo json_encode(PAYSTACK_PUBLIC_KEY); ?>;

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const name = document.getElementById('name').value.trim();
            
            if (!email || !name) {
                alert('Please fill in all fields');
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }
            
            initiatePayment(email, name);
        });

        function initiatePayment(email, name) {
            const reference = 'SUB-' + tier.toUpperCase() + '-' + Date.now();
            const payBtn = document.getElementById('payBtn');
            
            // Disable button
            payBtn.disabled = true;
            payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            const handler = PaystackPop.setup({
                key: paystackPublicKey,
                email: email,
                amount: amount * 100, // Convert to pesewas (GHS cents)
                currency: 'GHS',
                ref: reference,
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Subscription Tier",
                            variable_name: "subscription_tier",
                            value: tier
                        },
                        {
                            display_name: "Full Name",
                            variable_name: "customer_name",
                            value: name
                        }
                    ]
                },
                callback: function(response) {
                    // Payment successful
                    console.log('Payment successful:', response);
                    
                    // Store payment info and redirect to registration
                    window.location.href = 'register.php?role=planner&tier=' + tier + 
                                          '&payment_ref=' + response.reference + 
                                          '&email=' + encodeURIComponent(email) +
                                          '&name=' + encodeURIComponent(name);
                },
                onClose: function() {
                    // User closed payment popup
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<i class="bi bi-shield-lock me-2"></i>Pay GHS ' + amount.toLocaleString() + ' with Paystack';
                }
            });
            
            handler.openIframe();
        }
    </script>
</body>
</html>
