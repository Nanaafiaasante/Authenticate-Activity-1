<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Plan - VendorConnect Ghana</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/select_subscription.css">
</head>
<body>
    <!-- Decorative Elements -->
    <div class="botanical-corner botanical-tl"></div>
    <div class="botanical-corner botanical-tr"></div>
    <div class="botanical-corner botanical-bl"></div>
    <div class="botanical-corner botanical-br"></div>
    <div class="gold-accent gold-accent-1"></div>
    <div class="gold-accent gold-accent-2"></div>

    <!-- Logo -->
    <a href="../index.php" class="vc-logo">
        <div class="vc-logo-ring"></div>
        <div class="vc-logo-text">
            <span class="vc-logo-main">VendorConnect</span>
            <span class="vc-logo-sub">GHANA</span>
        </div>
    </a>

    <div class="subscription-container">
        <div class="subscription-header">
            <h1>Choose Your Plan</h1>
            <p>Select the perfect plan for your event planning business</p>
        </div>

        <div class="plans-grid">
            <!-- Starter Plan -->
            <div class="plan-card" data-tier="starter" data-price="0">
                <div class="plan-badge">Free Forever</div>
                <h2 class="plan-name">Starter</h2>
                <div class="plan-price">
                    <span class="currency">GHS</span>
                    <span class="amount">0</span>
                    <span class="period">/month</span>
                </div>
                <ul class="plan-features">
                    <li><span class="check">✓</span> Up to 5 products/services</li>
                    <li><span class="check">✓</span> Basic profile visibility</li>
                    <li><span class="check">✓</span> Customer inquiries</li>
                    <li><span class="check">✓</span> Standard support</li>
                    <li><span class="cross">✗</span> Featured listing</li>
                    <li><span class="cross">✗</span> Analytics dashboard</li>
                    <li><span class="cross">✗</span> Priority support</li>
                </ul>
                <button class="plan-btn" onclick="selectPlan('starter', 0)">
                    Get Started Free
                </button>
            </div>

            <!-- Premium Plan -->
            <div class="plan-card premium" data-tier="premium" data-price="50">
                <div class="plan-badge premium-badge">Most Popular</div>
                <h2 class="plan-name">Premium</h2>
                <div class="plan-price">
                    <span class="currency">GHS</span>
                    <span class="amount">50</span>
                    <span class="period">/month</span>
                </div>
                <ul class="plan-features">
                    <li><span class="check">✓</span> Unlimited products/services</li>
                    <li><span class="check">✓</span> Enhanced profile visibility</li>
                    <li><span class="check">✓</span> Featured in search results</li>
                    <li><span class="check">✓</span> Customer reviews & ratings</li>
                    <li><span class="check">✓</span> Analytics dashboard</li>
                    <li><span class="check">✓</span> Priority customer support</li>
                    <li><span class="check">✓</span> Marketing tools</li>
                </ul>
                <button class="plan-btn premium-btn" onclick="selectPlan('premium', 50)">
                    Start Premium Trial
                </button>
            </div>
        </div>

        <div class="subscription-note">
            <p>💡 You can upgrade or downgrade your plan anytime from your dashboard</p>
        </div>
    </div>

    <script>
        // Get registration data from URL or sessionStorage
        const urlParams = new URLSearchParams(window.location.search);
        const registrationData = sessionStorage.getItem('registrationData');
        
        if (!registrationData) {
            // If no registration data, redirect back to register
            window.location.href = 'register.php';
        }

        /**
         * Handle plan selection
         */
        function selectPlan(tier, price) {
            // Store selected plan
            const planData = {
                subscription_tier: tier,
                subscription_price: price
            };
            
            sessionStorage.setItem('selectedPlan', JSON.stringify(planData));
            
            // If starter (free), proceed directly to complete registration
            if (tier === 'starter') {
                completeRegistration(tier);
            } else {
                // If premium, redirect to payment
                initiatePayment(tier, price);
            }
        }

        /**
         * Complete registration for free tier
         */
        function completeRegistration(tier) {
            const userData = JSON.parse(registrationData);
            userData.subscription_tier = tier;
            userData.subscription_status = 'active';
            
            // Show loading
            showLoading('Creating your account...');
            
            // Submit registration
            fetch('../actions/register_customer_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.status === 'success') {
                    // Clear stored data
                    sessionStorage.removeItem('registrationData');
                    sessionStorage.removeItem('selectedPlan');
                    
                    // Show success and redirect
                    showSuccess('Account created successfully!');
                    setTimeout(() => {
                        window.location.href = '../login/login.php';
                    }, 2000);
                } else {
                    showError(data.message || 'Registration failed');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('An error occurred. Please try again.');
            });
        }

        /**
         * Initiate payment for premium tier
         */
        function initiatePayment(tier, price) {
            const userData = JSON.parse(registrationData);
            
            showLoading('Redirecting to payment...');
            
            // Initialize Paystack for subscription payment
            fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: price,
                    email: userData.customer_email,
                    metadata: {
                        payment_type: 'subscription',
                        subscription_tier: tier,
                        subscription_period: 'monthly'
                    }
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.status === 'success') {
                    // Store reference for verification
                    sessionStorage.setItem('subscriptionReference', data.reference);
                    
                    // Redirect to Paystack
                    window.location.href = data.authorization_url;
                } else {
                    showError(data.message || 'Failed to initialize payment');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showError('Payment initialization failed');
            });
        }

        /**
         * Show loading overlay
         */
        function showLoading(message) {
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                z-index: 10000;
            `;
            overlay.innerHTML = `
                <div style="width: 60px; height: 60px; border: 5px solid #fff; border-top-color: #047857; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="color: white; margin-top: 20px; font-size: 18px;">${message}</p>
                <style>
                    @keyframes spin { to { transform: rotate(360deg); } }
                </style>
            `;
            document.body.appendChild(overlay);
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) overlay.remove();
        }

        function showSuccess(message) {
            alert('✓ ' + message);
        }

        function showError(message) {
            alert('✗ ' + message);
        }
    </script>
</body>
</html>
