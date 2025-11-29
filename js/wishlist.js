/**
 * Wishlist Page JavaScript
 * Handles wishlist display and management
 */

// Load wishlist items on page load
document.addEventListener('DOMContentLoaded', () => {
    loadWishlistItems();
});

/**
 * Load wishlist items
 */
function loadWishlistItems() {
    fetch('../actions/get_wishlist_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayWishlistItems(data.wishlist_items);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to load wishlist items');
        });
}

/**
 * Display wishlist items
 */
function displayWishlistItems(items) {
    const container = document.getElementById('wishlistItemsContainer');
    
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="empty-wishlist">
                <i class="bi bi-heart"></i>
                <h3>Your wishlist is empty</h3>
                <p>Save items you love to your wishlist!</p>
                <a href="all_products.php" class="btn-shop-now">
                    <i class="bi bi-shop me-2"></i>Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    let html = '<div class="row g-4">';
    
    items.forEach(item => {
        const imageUrl = item.product_image ? `../${item.product_image}` : '../uploads/default-product.jpg';
        const price = parseFloat(item.product_price).toFixed(2);
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="wishlist-card">
                    <button class="btn-remove-wishlist" onclick="removeFromWishlist(${item.product_id})" title="Remove from wishlist">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    <img src="${imageUrl}" alt="${escapeHtml(item.product_title)}" class="wishlist-image" 
                         onerror="this.src='../uploads/default-product.jpg'"
                         onclick="window.location.href='single_product.php?id=${item.product_id}'"
                         style="cursor: pointer;">
                    <div class="wishlist-body">
                        <h5 class="wishlist-title" onclick="window.location.href='single_product.php?id=${item.product_id}'" style="cursor: pointer;">
                            ${escapeHtml(item.product_title)}
                        </h5>
                        <p class="wishlist-vendor">
                            <i class="bi bi-person"></i> by ${escapeHtml(item.vendor_business_name || item.vendor_name)}
                        </p>
                        <div class="wishlist-price">GHS ${price}</div>
                        <div class="wishlist-badges">
                            <span class="badge-custom">${escapeHtml(item.cat_name)}</span>
                            <span class="badge-custom badge-brand">${escapeHtml(item.brand_name)}</span>
                        </div>
                        <div class="wishlist-actions">
                            <button class="btn-add-to-cart" onclick="addToCartFromWishlist(${item.product_id})">
                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                            </button>
                            <a href="single_product.php?id=${item.product_id}" class="btn-view-details">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Remove from wishlist
 */
function removeFromWishlist(productId) {
    fetch('../actions/remove_from_wishlist_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccess(data.message);
            loadWishlistItems(); // Reload wishlist
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to remove from wishlist');
    });
}

/**
 * Add to cart from wishlist
 */
function addToCartFromWishlist(productId) {
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1,
            selected_items: []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccess(data.message + ' - <a href="cart.php" style="color: white; text-decoration: underline;">View Cart</a>');
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to add to cart');
    });
}

/**
 * Show success message
 */
function showSuccess(message) {
    showAlert('success', message);
}

/**
 * Show error message
 */
function showError(message) {
    showAlert('error', message);
}

/**
 * Show alert
 */
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="bi ${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 380px;';
        document.body.appendChild(alertContainer);
    }
    
    alertContainer.innerHTML = alertHtml;
    
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) alert.remove();
    }, 4000);
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
