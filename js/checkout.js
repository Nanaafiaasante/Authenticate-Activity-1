/**
 * Checkout JavaScript
 * Handles checkout process including simulated payment modal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Load checkout summary if we're on checkout page
    if (document.getElementById('checkoutSummary')) {
        loadCheckoutSummary();
    }
    
    // Set up payment simulation modal event listeners
    setupPaymentModal();
});

/**
 * Load checkout summary
 */
function loadCheckoutSummary() {
    const container = document.getElementById('checkoutSummary');
    if (!container) return;
    
    // Show loading
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading order summary...</p>
        </div>
    `;
    
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (!data.cart_items || data.cart_items.length === 0) {
                    // Cart is empty, redirect to cart page
                    window.location.href = 'cart.php';
                    return;
                }
                
                displayCheckoutSummary(data.cart_items, data.cart_total, data.cart_total_raw);
            } else {
                showCheckoutError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCheckoutError('Failed to load order summary');
        });
}

/**
 * Display checkout summary
 */
function displayCheckoutSummary(items, total, totalRaw) {
    const container = document.getElementById('checkoutSummary');
    if (!container) return;
    
    let html = '<div class="checkout-items">';
    
    items.forEach(item => {
        const imagePath = item.product_image ? `../${item.product_image}` : '../images/placeholder.png';
        html += `
            <div class="checkout-item">
                <div class="checkout-item-image">
                    <img src="${imagePath}" alt="${item.product_title}">
                    <span class="item-quantity-badge">${item.qty}</span>
                </div>
                <div class="checkout-item-details">
                    <h6>${item.product_title}</h6>
                    <p class="text-muted">GHS ${parseFloat(item.product_price).toFixed(2)} × ${item.qty}</p>
                </div>
                <div class="checkout-item-total">
                    <strong>GHS ${parseFloat(item.subtotal).toFixed(2)}</strong>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Add totals section
    html += `
        <div class="checkout-totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>GHS ${total}</span>
            </div>
            <div class="total-row">
                <span>Shipping:</span>
                <span class="text-success">FREE</span>
            </div>
            <div class="total-row total-final">
                <span><strong>Total:</strong></span>
                <span><strong>GHS ${total}</strong></span>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Store total for later use
    container.dataset.total = totalRaw;
    
    // Enable proceed to payment button
    const proceedBtn = document.getElementById('proceedToPaymentBtn');
    if (proceedBtn) {
        proceedBtn.disabled = false;
    }
}

/**
 * Set up payment modal - Now directly redirects to Paystack
 */
function setupPaymentModal() {
    const proceedBtn = document.getElementById('proceedToPaymentBtn');
    
    if (proceedBtn) {
        proceedBtn.addEventListener('click', function() {
            // Directly process checkout without showing modal
            processCheckout();
        });
    }
}

// Modal functions removed - now using direct Paystack redirect

/**
 * Process checkout via Paystack - Direct redirect, no modal
 */
function processCheckout() {
    const proceedBtn = document.getElementById('proceedToPaymentBtn');
    
    // Disable button and show loading
    if (proceedBtn) {
        proceedBtn.disabled = true;
        proceedBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirecting to Paystack...';
    }
    
    // Get cart total
    const summary = document.getElementById('checkoutSummary');
    const total = summary ? parseFloat(summary.dataset.total) : 0;
    
    // First, get cart data to retrieve customer email
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(cartData => {
            if (cartData.status !== 'success' || !cartData.customer_email) {
                throw new Error('Could not retrieve customer email');
            }
            
            const email = cartData.customer_email;
            
            // Initialize Paystack transaction
            return fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: total,
                    email: email
                })
            });
        })
        .then(response => response.json())
        .then(data => {
            console.log('Paystack init response:', data);
            
            if (data.status === 'success') {
                // Get cart items for storage
                return fetch('../actions/get_cart_action.php')
                    .then(response => response.json())
                    .then(cartData => {
                        // Store checkout data for verification later
                        localStorage.setItem('checkoutData', JSON.stringify({
                            total: total,
                            reference: data.reference,
                            items: cartData.cart_items || []
                        }));
                        
                        // Redirect directly to Paystack
                        window.location.href = data.authorization_url;
                    });
            } else {
                throw new Error(data.message || 'Failed to initialize payment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Use cart-style error message
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(error.message || 'Failed to connect to payment gateway. Please try again.');
            } else {
                showCheckoutError(error.message || 'Failed to connect to payment gateway. Please try again.');
            }
            
            // Re-enable button
            if (proceedBtn) {
                proceedBtn.disabled = false;
                proceedBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Pay with Paystack';
            }
        });
}

/**
 * Show order success message
 */
function showOrderSuccess(orderData) {
    const container = document.querySelector('.checkout-container') || document.body;
    
    const successHtml = `
        <div class="order-success-message">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2>Order Placed Successfully!</h2>
            <p class="lead">Thank you for your purchase</p>
            
            <div class="order-details-card">
                <h4>Order Details</h4>
                <table class="order-details-table">
                    <tr>
                        <td><strong>Order ID:</strong></td>
                        <td>#${orderData.order_id}</td>
                    </tr>
                    <tr>
                        <td><strong>Invoice Number:</strong></td>
                        <td>${orderData.invoice_no}</td>
                    </tr>
                    <tr>
                        <td><strong>Order Date:</strong></td>
                        <td>${orderData.order_date}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td>${orderData.currency} ${orderData.total_amount}</td>
                    </tr>
                    <tr>
                        <td><strong>Items:</strong></td>
                        <td>${orderData.items_count} item(s)</td>
                    </tr>
                </table>
            </div>
            
            <div class="success-actions">
                <a href="../index.php" class="btn btn-primary">
                    <i class="bi bi-house me-2"></i>Go to Home
                </a>
                <a href="orders.php" class="btn btn-outline-primary">
                    <i class="bi bi-bag-check me-2"></i>View My Orders
                </a>
            </div>
            
            <p class="text-muted mt-4">
                <i class="bi bi-envelope me-2"></i>
                A confirmation email has been sent to your registered email address.
            </p>
        </div>
    `;
    
    // Replace checkout content with success message
    const checkoutContent = document.querySelector('.checkout-content');
    if (checkoutContent) {
        checkoutContent.innerHTML = successHtml;
    } else {
        container.innerHTML = successHtml;
    }
    
    // Update cart count
    if (typeof updateCartCount === 'function') {
        updateCartCount(0);
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Show checkout error
 */
function showCheckoutError(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    let alertContainer = document.getElementById('checkoutAlertContainer');
    
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'checkoutAlertContainer';
        alertContainer.className = 'container mt-3';
        
        const mainContainer = document.querySelector('.checkout-container');
        if (mainContainer) {
            mainContainer.insertBefore(alertContainer, mainContainer.firstChild);
        } else {
            document.body.insertBefore(alertContainer, document.body.firstChild);
        }
    }
    
    alertContainer.innerHTML = alertHtml;
    
    // Scroll to alert
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 150);
        }
    }, 5000);
}

/**
 * Show checkout success message
 */
function showCheckoutSuccess(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    let alertContainer = document.getElementById('checkoutAlertContainer');
    
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'checkoutAlertContainer';
        alertContainer.className = 'container mt-3';
        
        const mainContainer = document.querySelector('.checkout-container');
        if (mainContainer) {
            mainContainer.insertBefore(alertContainer, mainContainer.firstChild);
        }
    }
    
    alertContainer.innerHTML = alertHtml;
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
