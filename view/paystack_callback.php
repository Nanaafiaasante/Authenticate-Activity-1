<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - VendorConnect Ghana</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
        }
        
        .callback-container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }
        
        .spinner {
            width: 80px;
            height: 80px;
            border: 6px solid var(--gray-light);
            border-top-color: var(--emerald);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .callback-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--emerald-dark);
            margin-bottom: 16px;
        }
        
        .callback-message {
            font-size: 16px;
            color: var(--gray-medium);
            margin-bottom: 12px;
        }
        
        .callback-note {
            font-size: 14px;
            color: var(--gray-medium);
            font-style: italic;
        }
        
        .error-container {
            display: none;
            padding: 20px;
            background: #fee2e2;
            border: 2px solid #fecaca;
            border-radius: 12px;
            margin-top: 20px;
        }
        
        .error-message {
            color: #991b1b;
            font-weight: 600;
            margin-bottom: 16px;
        }
        
        .retry-btn {
            display: inline-block;
            padding: 12px 32px;
            background: var(--emerald);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .retry-btn:hover {
            background: var(--emerald-dark);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="callback-container">
        <div class="spinner"></div>
        <h1 class="callback-title">Verifying Payment</h1>
        <p class="callback-message">Please wait while we confirm your payment...</p>
        <p class="callback-note">This should only take a few seconds.</p>
        
        <div class="error-container" id="errorContainer">
            <p class="error-message" id="errorMessage"></p>
            <a href="../view/checkout.php" class="retry-btn">Return to Checkout</a>
        </div>
    </div>

    <script>
        // Get reference from URL
        const urlParams = new URLSearchParams(window.location.search);
        const reference = urlParams.get('reference') || urlParams.get('trxref');
        
        console.log('Payment callback - URL:', window.location.href);
        console.log('Payment reference:', reference);
        console.log('All URL params:', Object.fromEntries(urlParams.entries()));
        
        if (!reference) {
            showError('No payment reference found in callback URL');
        } else {
            verifyPayment(reference);
        }
        
        /**
         * Verify payment with backend
         */
        function verifyPayment(reference) {
            // Get cart data from session/localStorage if available
            const cartData = localStorage.getItem('checkoutData');
            let requestData = {
                reference: reference
            };
            
            if (cartData) {
                try {
                    const parsed = JSON.parse(cartData);
                    requestData.cart_items = parsed.items;
                    requestData.total_amount = parsed.total;
                } catch (e) {
                    console.warn('Could not parse cart data:', e);
                }
            }
            
            console.log('Verifying payment with data:', requestData);
            
            fetch('../actions/paystack_verify_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Verification response:', data);
                
                if (data.status === 'success' && data.verified) {
                    // Clear cart data
                    localStorage.removeItem('checkoutData');
                    
                    // Redirect to success page with order details
                    const params = new URLSearchParams({
                        invoice: data.invoice_no,
                        amount: data.total_amount,
                        currency: data.currency,
                        date: data.order_date,
                        items: data.item_count,
                        reference: data.payment_reference,
                        method: data.payment_method
                    });
                    
                    window.location.href = 'payment_success.php?' + params.toString();
                } else {
                    showError(data.message || 'Payment verification failed');
                }
            })
            .catch(error => {
                console.error('Verification error:', error);
                showError('Failed to verify payment. Please contact support with reference: ' + reference);
            });
        }
        
        /**
         * Show error message
         */
        function showError(message) {
            document.querySelector('.spinner').style.display = 'none';
            document.querySelector('.callback-title').textContent = 'Verification Failed';
            document.querySelector('.callback-message').style.display = 'none';
            document.querySelector('.callback-note').style.display = 'none';
            
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            
            errorMessage.textContent = message;
            errorContainer.style.display = 'block';
        }
    </script>
</body>
</html>
