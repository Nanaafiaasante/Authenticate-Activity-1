<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - VendorConnect Ghana</title>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--cream);
            overflow-x: hidden;
        }
        
        .success-container {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
            position: relative;
            z-index: 10;
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--emerald-light) 0%, var(--emerald) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease;
        }
        
        .success-icon::before {
            content: '✓';
            font-size: 60px;
            color: white;
            font-weight: bold;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-title {
            font-size: 32px;
            font-weight: 700;
            color: var(--emerald-dark);
            margin-bottom: 16px;
        }
        
        .success-subtitle {
            font-size: 18px;
            color: var(--gray-medium);
            margin-bottom: 40px;
        }
        
        .order-details {
            background: var(--gray-light);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: left;
        }
        
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .order-row:last-child {
            border-bottom: none;
        }
        
        .order-label {
            font-weight: 600;
            color: var(--gray-dark);
        }
        
        .order-value {
            color: var(--gray-medium);
            text-align: right;
        }
        
        .order-value.highlight {
            color: var(--emerald);
            font-weight: 700;
            font-size: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 14px 32px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--emerald);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--emerald-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(4, 120, 87, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: var(--emerald);
            border: 2px solid var(--emerald);
        }
        
        .btn-secondary:hover {
            background: var(--emerald);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Floating particles animation */
        .particle {
            position: fixed;
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #C9A961, #D4AF37);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            opacity: 0;
            box-shadow: 0 0 10px rgba(201, 169, 97, 0.5);
        }
        
        /* Elegant rings animation */
        .ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 3px solid rgba(201, 169, 97, 0.3);
            border-radius: 50%;
            pointer-events: none;
            z-index: 5;
        }
        
        /* Success checkmark animation enhancement */
        .success-icon::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border: 2px solid rgba(201, 169, 97, 0.2);
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 0;
            }
        }
        
        @media (max-width: 576px) {
            .success-container {
                padding: 40px 24px;
            }
            
            .success-title {
                font-size: 24px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon"></div>
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-subtitle">Thank you for your order. Your payment has been confirmed.</p>
        
        <div class="order-details">
            <div class="order-row">
                <span class="order-label">Invoice Number:</span>
                <span class="order-value" id="invoiceNo">-</span>
            </div>
            <div class="order-row">
                <span class="order-label">Amount Paid:</span>
                <span class="order-value highlight" id="amountPaid">-</span>
            </div>
            <div class="order-row">
                <span class="order-label">Order Date:</span>
                <span class="order-value" id="orderDate">-</span>
            </div>
            <div class="order-row">
                <span class="order-label">Items Ordered:</span>
                <span class="order-value" id="itemCount">-</span>
            </div>
            <div class="order-row">
                <span class="order-label">Payment Method:</span>
                <span class="order-value" id="paymentMethod">-</span>
            </div>
            <div class="order-row">
                <span class="order-label">Transaction Reference:</span>
                <span class="order-value" id="reference" style="font-size: 12px; word-break: break-all;">-</span>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="all_products.php" class="btn-action btn-primary">Continue Shopping</a>
            <a href="cart.php" class="btn-action btn-secondary">View Orders</a>
        </div>
    </div>

    <script>
        // Get order details from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        // Populate order details
        document.getElementById('invoiceNo').textContent = urlParams.get('invoice') || 'N/A';
        document.getElementById('amountPaid').textContent = 'GHS ' + (urlParams.get('amount') || '0.00');
        document.getElementById('orderDate').textContent = urlParams.get('date') || 'N/A';
        document.getElementById('itemCount').textContent = (urlParams.get('items') || '0') + ' items';
        document.getElementById('paymentMethod').textContent = urlParams.get('method') || 'Paystack';
        document.getElementById('reference').textContent = urlParams.get('reference') || 'N/A';
        
        // Create elegant particle animation
        createParticles();
        createRings();
        
        /**
         * Create elegant floating particles
         */
        function createParticles() {
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                setTimeout(() => {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    
                    // Random position around the screen
                    const startX = Math.random() * window.innerWidth;
                    const startY = Math.random() * window.innerHeight;
                    
                    particle.style.left = startX + 'px';
                    particle.style.top = startY + 'px';
                    
                    document.body.appendChild(particle);
                    
                    animateParticle(particle);
                }, i * 100);
            }
        }
        
        /**
         * Animate individual particle with elegant floating effect
         */
        function animateParticle(element) {
            const startTime = Date.now();
            const duration = 3000 + Math.random() * 2000;
            const startX = parseFloat(element.style.left);
            const startY = parseFloat(element.style.top);
            const targetY = startY - 200 - Math.random() * 300;
            const driftX = (Math.random() - 0.5) * 200;
            
            function animate() {
                const elapsed = Date.now() - startTime;
                const progress = elapsed / duration;
                
                if (progress < 1) {
                    // Ease out cubic for smooth deceleration
                    const easeProgress = 1 - Math.pow(1 - progress, 3);
                    
                    const currentY = startY + (targetY - startY) * easeProgress;
                    const currentX = startX + driftX * easeProgress + Math.sin(progress * Math.PI * 4) * 20;
                    
                    element.style.top = currentY + 'px';
                    element.style.left = currentX + 'px';
                    
                    // Fade in then fade out
                    if (progress < 0.2) {
                        element.style.opacity = progress * 5;
                    } else if (progress > 0.7) {
                        element.style.opacity = (1 - progress) * 3.33;
                    } else {
                        element.style.opacity = 1;
                    }
                    
                    // Scale effect
                    const scale = 0.5 + Math.sin(progress * Math.PI) * 0.5;
                    element.style.transform = `scale(${scale})`;
                    
                    requestAnimationFrame(animate);
                } else {
                    element.remove();
                }
            }
            
            animate();
        }
        
        /**
         * Create expanding rings animation
         */
        function createRings() {
            const container = document.querySelector('.success-container');
            const ringCount = 3;
            
            for (let i = 0; i < ringCount; i++) {
                setTimeout(() => {
                    const ring = document.createElement('div');
                    ring.className = 'ring';
                    container.appendChild(ring);
                    animateRing(ring);
                }, i * 400);
            }
        }
        
        /**
         * Animate expanding ring
         */
        function animateRing(element) {
            const startTime = Date.now();
            const duration = 2000;
            
            function animate() {
                const elapsed = Date.now() - startTime;
                const progress = elapsed / duration;
                
                if (progress < 1) {
                    const scale = 1 + progress * 3;
                    const opacity = 1 - progress;
                    
                    element.style.transform = `translate(-50%, -50%) scale(${scale})`;
                    element.style.opacity = opacity;
                    
                    requestAnimationFrame(animate);
                } else {
                    element.remove();
                }
            }
            
            animate();
        }
        
        // Update cart count badge to 0
        const badges = document.querySelectorAll('.cart-count-badge');
        badges.forEach(badge => {
            badge.textContent = '0';
            badge.style.display = 'none';
        });
    </script>
</body>
</html>
