/**
 * Cart JavaScript
 * Handles all cart-related UI interactions and AJAX operations
 */

// Global cart state
let cartState = {
    items: [],
    count: 0,
    total: 0
};

/**
 * Initialize cart functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count on page load
    updateCartCount();
    
    // If we're on the cart page, load cart items
    if (document.getElementById('cartItemsContainer')) {
        loadCartItems();
    }
    
    // Set up event listeners for add to cart buttons (delegated)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-btn')) {
            console.log('Cart button clicked - event listener triggered');
            e.preventDefault();
            const btn = e.target.closest('.add-to-cart-btn');
            const productId = btn.dataset.productId;
            const quantityInputId = btn.dataset.quantityInput;
            
            let quantity = 1;
            // Check if there's a quantity input reference
            if (quantityInputId) {
                const input = document.getElementById(quantityInputId);
                quantity = input ? parseInt(input.value) || 1 : 1;
            } else {
                // Otherwise use data-quantity attribute
                quantity = btn.dataset.quantity || 1;
            }
            
            addToCart(productId, quantity);
        }
    });
});

/**
 * Add item to cart
 */
function addToCart(productId, quantity = 1) {
    // Collect selected package items
    const selectedItems = getSelectedPackageItems();
    
    // Check if there are any package items on the page
    const packageItemsExist = document.querySelectorAll('.package-item-check').length > 0;
    
    // Validate: if package items exist, at least one must be selected
    if (packageItemsExist && selectedItems.length === 0) {
        showErrorMessage('Please select at least one package item before adding to cart.');
        return;
    }
    
    // Show loading state
    showLoadingMessage('Adding to cart...');
    
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity),
            selected_items: selectedItems
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.status === 'success') {
            const message = data.message || 'Item added to cart successfully';
            if (message && message !== 'undefined') {
                showSuccessMessage(message);
            }
            if (data.cart_count !== undefined && data.cart_count !== null) {
                updateCartCount(data.cart_count);
            }
            
            // If we're on the cart page, reload items
            if (document.getElementById('cartItemsContainer')) {
                loadCartItems();
            }
        } else {
            const message = (data && data.message) || 'Failed to add item to cart';
            showErrorMessage(message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Failed to add item to cart. Please try again.');
    });
}

/**
 * Get selected package items
 */
function getSelectedPackageItems() {
    const checkboxes = document.querySelectorAll('.package-item-check:checked');
    const selectedItems = [];
    
    checkboxes.forEach(checkbox => {
        selectedItems.push({
            item_id: checkbox.dataset.itemId,
            item_name: checkbox.dataset.itemName
        });
    });
    
    return selectedItems;
}

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    showLoadingMessage('Removing item...');
    
    fetch('../actions/remove_from_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessMessage(data.message);
            updateCartCount(data.cart_count);
            
            // Reload cart items
            if (document.getElementById('cartItemsContainer')) {
                loadCartItems();
            }
        } else {
            showErrorMessage(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Failed to remove item. Please try again.');
    });
}

/**
 * Update cart item quantity
 */
function updateCartQuantity(productId, quantity) {
    fetch('../actions/update_quantity_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            updateCartCount(data.cart_count);
            
            // Update the display
            if (document.getElementById('cartItemsContainer')) {
                loadCartItems();
            }
            
            // Show message only if item was removed
            if (quantity === 0) {
                showSuccessMessage(data.message);
            }
        } else {
            showErrorMessage(data.message);
            // Reload cart to fix any discrepancies
            if (document.getElementById('cartItemsContainer')) {
                loadCartItems();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Failed to update quantity. Please try again.');
    });
}

/**
 * Empty entire cart
 */
function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
        return;
    }
    
    showLoadingMessage('Emptying cart...');
    
    fetch('../actions/empty_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showSuccessMessage(data.message);
            updateCartCount(0);
            
            // Reload cart items (will show empty cart message)
            if (document.getElementById('cartItemsContainer')) {
                loadCartItems();
            }
        } else {
            showErrorMessage(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Failed to empty cart. Please try again.');
    });
}

/**
 * Load and display cart items
 */
function loadCartItems() {
    const container = document.getElementById('cartItemsContainer');
    if (!container) return;
    
    // Show loading
    container.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading cart...</p>
        </div>
    `;
    
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                cartState.items = data.cart_items;
                cartState.count = data.cart_count;
                cartState.total = data.cart_total_raw;
                
                displayCartItems(data.cart_items, data.cart_total);
                updateCartCount(data.cart_count);
            } else {
                showErrorMessage(data.message);
                container.innerHTML = '<div class="alert alert-danger">Failed to load cart items</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="alert alert-danger">An error occurred while loading cart</div>';
        });
}

/**
 * Display cart items in the UI
 */
function displayCartItems(items, total) {
    const container = document.getElementById('cartItemsContainer');
    const totalElement = document.getElementById('cartTotal');
    const subtotalElement = document.getElementById('cartSubtotal');
    
    if (!container) return;
    
    // Check if cart is empty
    if (!items || items.length === 0) {
        container.innerHTML = `
            <div class="empty-cart-message">
                <i class="bi bi-cart-x"></i>
                <h3>Your cart is empty</h3>
                <p>Start adding some products to your cart!</p>
            </div>
        `;
        
        // Hide checkout button and empty cart button
        const checkoutBtn = document.getElementById('checkoutBtn');
        const emptyCartBtn = document.getElementById('emptyCartBtn');
        if (checkoutBtn) checkoutBtn.style.display = 'none';
        if (emptyCartBtn) emptyCartBtn.style.display = 'none';
        
        if (totalElement) totalElement.textContent = 'GHS 0.00';
        if (subtotalElement) subtotalElement.textContent = 'GHS 0.00';
        
        return;
    }
    
    // Build cart items HTML
    let html = '';
    items.forEach(item => {
        const imagePath = item.product_image ? `../${item.product_image}` : '../images/placeholder.png';
        
        // Parse selected items if available
        let selectedItemsHtml = '';
        if (item.selected_items) {
            try {
                const selectedItems = JSON.parse(item.selected_items);
                if (selectedItems && selectedItems.length > 0) {
                    selectedItemsHtml = '<div class="cart-package-items"><div class="cart-package-title"><i class="bi bi-check2-square"></i> Included:</div><div class="cart-package-list">';
                    selectedItems.forEach(pkgItem => {
                        selectedItemsHtml += `<span class="cart-package-item"><i class="bi bi-check-circle-fill"></i>${escapeHtml(pkgItem.item_name)}</span>`;
                    });
                    selectedItemsHtml += '</div></div>';
                }
            } catch (e) {
                console.error('Error parsing selected items:', e);
            }
        }
        
        html += `
            <div class="cart-item" data-product-id="${item.p_id}">
                <div class="cart-item-image">
                    <img src="${imagePath}" alt="${item.product_title}">
                </div>
                <div class="cart-item-details">
                    <h5 class="cart-item-title">${item.product_title}</h5>
                    <p class="cart-item-meta">
                        ${item.cat_name ? `<span class="badge bg-secondary">${item.cat_name}</span>` : ''}
                        ${item.brand_name ? `<span class="badge bg-info">${item.brand_name}</span>` : ''}
                    </p>
                    <p class="cart-item-price">GHS ${parseFloat(item.product_price).toFixed(2)}</p>
                    ${selectedItemsHtml}
                </div>
                <div class="cart-item-quantity">
                    <label>Quantity</label>
                    <div class="quantity-controls">
                        <button class="qty-btn qty-minus" onclick="changeQuantity(${item.p_id}, ${parseInt(item.qty) - 1})">
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" class="qty-input" value="${item.qty}" min="1" 
                               onchange="changeQuantity(${item.p_id}, parseInt(this.value))">
                        <button class="qty-btn qty-plus" onclick="changeQuantity(${item.p_id}, ${parseInt(item.qty) + 1})">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="cart-item-subtotal">
                    <label>Subtotal</label>
                    <p class="item-subtotal-amount">GHS ${parseFloat(item.subtotal).toFixed(2)}</p>
                </div>
                <div class="cart-item-remove">
                    <button class="btn-remove" onclick="removeFromCart(${item.p_id})" title="Remove item">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Update totals
    if (totalElement) totalElement.textContent = `GHS ${total}`;
    if (subtotalElement) subtotalElement.textContent = `GHS ${total}`;
    
    // Show checkout and empty cart buttons
    const checkoutBtn = document.getElementById('checkoutBtn');
    const emptyCartBtn = document.getElementById('emptyCartBtn');
    if (checkoutBtn) checkoutBtn.style.display = 'inline-block';
    if (emptyCartBtn) emptyCartBtn.style.display = 'inline-block';
}

/**
 * Change quantity (called from UI)
 */
function changeQuantity(productId, newQuantity) {
    newQuantity = parseInt(newQuantity);
    
    if (isNaN(newQuantity) || newQuantity < 0) {
        showErrorMessage('Invalid quantity');
        loadCartItems(); // Reload to reset
        return;
    }
    
    updateCartQuantity(productId, newQuantity);
}

/**
 * Update cart count badge
 */
function updateCartCount(count = null) {
    if (count === null) {
        // Fetch cart count
        fetch('../actions/get_cart_action.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartCountDisplay(data.cart_count);
                }
            })
            .catch(error => console.error('Error fetching cart count:', error));
    } else {
        updateCartCountDisplay(count);
    }
}

/**
 * Update cart count display in UI
 */
function updateCartCountDisplay(count) {
    const badges = document.querySelectorAll('.cart-count-badge');
    badges.forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    });
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
    // Use Bootstrap toast or alert
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show cart-alert" role="alert">
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    showAlert(alertHtml);
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show cart-alert" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    showAlert(alertHtml);
}

/**
 * Show loading message
 */
function showLoadingMessage(message) {
    const alertHtml = `
        <div class="alert alert-info cart-alert" role="alert">
            <span class="spinner-border spinner-border-sm me-2"></span>${message}
        </div>
    `;
    showAlert(alertHtml);
}

/**
 * Show alert in designated container or create one (Snackbar style)
 */
function showAlert(alertHtml) {
    let alertContainer = document.getElementById('alertContainer');
    
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.cssText = 'position: fixed !important; top: 80px !important; left: 20px !important; z-index: 99999 !important; max-width: 380px !important;';
        document.body.appendChild(alertContainer);
    } else {
        // Ensure position is correct even if container exists
        alertContainer.style.cssText = 'position: fixed !important; top: 80px !important; left: 20px !important; z-index: 99999 !important; max-width: 380px !important;';
    }
    
    alertContainer.innerHTML = alertHtml;
    
    // Add slide-in animation
    const alert = alertContainer.querySelector('.cart-alert');
    if (alert) {
        alert.style.animation = 'slideInLeft 0.3s ease-out';
    }
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        if (alert) {
            alert.style.animation = 'slideOutLeft 0.3s ease-out';
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 300);
        }
    }, 3000);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
