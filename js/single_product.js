/**
 * Single Product JavaScript
 * Handles single product detail display
 */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadProductDetails();
});

/**
 * Load product details
 */
function loadProductDetails() {
    const container = document.getElementById('productContainer');
    
    fetch(`../actions/view_single_product_action.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayProductDetails(data.data);
                // Load package items after product details are displayed
                loadPackageItems(productId);
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>${data.message}
                        <div class="mt-3">
                            <a href="all_products.php" class="btn btn-primary-custom">
                                Browse All Products
                            </a>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>An error occurred while loading product details
                </div>
            `;
        });
}

/**
 * Display product details
 */
function displayProductDetails(product) {
    const container = document.getElementById('productContainer');
    const imageUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
    const price = parseFloat(product.product_price).toFixed(2);
    const keywords = product.product_keywords ? product.product_keywords.split(',') : [];
    
    const html = `
        <div class="product-detail-card">
            <div class="product-content">
                <div class="product-image-section">
                    <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" 
                         class="product-main-image" onerror="this.src='../uploads/default-product.jpg'">
                </div>
                <div class="product-info-section">
                    <h1 class="product-title-large">${escapeHtml(product.product_title)}</h1>
                    
                    <div class="product-price-large">GHS ${price}</div>
                    
                    <div class="badge-container">
                        <span class="badge-large badge-category">${escapeHtml(product.cat_name)}</span>
                        <span class="badge-large badge-brand">${escapeHtml(product.brand_name)}</span>
                    </div>
                    
                    <div class="product-description">
                        ${product.product_desc ? escapeHtml(product.product_desc) : 'No description available.'}
                    </div>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-tag"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Product ID</div>
                                <div class="meta-value">#${product.product_id}</div>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-grid"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Category</div>
                                <div class="meta-value">${escapeHtml(product.cat_name)}</div>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-icon">
                                <i class="bi bi-bag"></i>
                            </div>
                            <div class="meta-content">
                                <div class="meta-label">Brand</div>
                                <div class="meta-value">${escapeHtml(product.brand_name)}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${keywords.length > 0 ? `
                        <div class="keywords-section">
                            <div class="keywords-title">Keywords</div>
                            ${keywords.map(kw => `<span class="keyword-tag">${escapeHtml(kw.trim())}</span>`).join('')}
                        </div>
                    ` : ''}
                    
                    <div id="packageItemsContainer" class="package-items-container"></div>
                    
                    <div class="quantity-section">
                        <label class="quantity-label">Quantity</label>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(-1)" title="Decrease quantity">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="qty-input" id="productQuantity" value="1" min="1" max="99">
                            <button class="qty-btn" onclick="updateQuantity(1)" title="Increase quantity">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn-cart add-to-cart-btn" data-product-id="${product.product_id}" data-quantity-input="productQuantity">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                        <button class="btn-wishlist" id="wishlistBtn" data-product-id="${product.product_id}" onclick="toggleWishlist(${product.product_id})">
                            <i class="bi bi-heart me-2"></i>Add to Wishlist
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="vendor-card">
            <div class="vendor-header">
                <div class="vendor-header-left">
                    <h4>
                        <i class="bi bi-shop me-2"></i>${escapeHtml(product.vendor_business_name || product.vendor_name)}
                    </h4>
                    <div class="vendor-location">
                        <i class="bi bi-geo-alt me-1"></i>${escapeHtml(product.vendor_city)}${product.vendor_country ? ', ' + escapeHtml(product.vendor_country) : ''}
                    </div>
                </div>
                ${product.vendor_rating && parseFloat(product.vendor_rating) > 0 ? `
                <div class="vendor-rating-box">
                    <div class="rating-stars">
                        <i class="bi bi-star-fill"></i> ${parseFloat(product.vendor_rating).toFixed(1)}
                    </div>
                    <div class="rating-text">
                        ${product.vendor_review_count || 0} reviews
                    </div>
                </div>
                ` : ''}
            </div>
            
            <div class="vendor-body">
                ${product.vendor_about ? `
                <div class="vendor-about-box">
                    <div class="vendor-about-box-title">
                        <i class="bi bi-info-circle me-2"></i>About This Vendor
                    </div>
                    <p class="vendor-about-box-text">${escapeHtml(product.vendor_about)}</p>
                </div>
                ` : ''}
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="contact-item">
                            <div class="contact-icon phone">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="contact-label">Phone</div>
                                <a href="tel:${product.vendor_contact}" class="contact-link">
                                    ${escapeHtml(product.vendor_contact)}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="contact-item">
                            <div class="contact-icon email">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="contact-label">Email</div>
                                <a href="mailto:${product.vendor_email}" class="contact-link">
                                    ${escapeHtml(product.vendor_email)}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn-view-all-products" onclick="showVendorProfile(${product.user_id})">
                    <i class="bi bi-eye me-2"></i>View All Products & Services
                </button>
            </div>
            
            ${(product.vendor_address || product.vendor_location) ? `
            <div style="padding: 1.5rem; border-top: 1px solid rgba(234, 224, 218, 0.6);">
                <div class="meta-item">
                    <div class="meta-icon">
                        <i class="bi bi-pin-map-fill"></i>
                    </div>
                    <div class="meta-content">
                        <div class="meta-label">Service Address</div>
                        <div class="meta-value">${escapeHtml(product.vendor_address || product.vendor_location)}</div>
                    </div>
                </div>
            </div>
            ` : ''}
            
            ${product.vendor_latitude && product.vendor_longitude ? `
            <div style="padding: 1.5rem; border-top: 1px solid rgba(234, 224, 218, 0.6);">
                <div class="meta-item">
                    <div class="meta-icon">
                        <i class="bi bi-compass"></i>
                    </div>
                    <div class="meta-content">
                        <div class="meta-label">Coordinates</div>
                        <div class="meta-value">
                            Lat: ${product.vendor_latitude}, Long: ${product.vendor_longitude}
                            <a href="https://www.google.com/maps?q=${product.vendor_latitude},${product.vendor_longitude}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary ms-3">
                                <i class="bi bi-map me-1"></i>View on Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
        </div>
        
        <!-- Customer Reviews Section -->
        <div class="reviews-section" id="reviewsSection">
            <div class="reviews-header">
                <h4><i class="bi bi-star-fill me-2"></i>Customer Reviews</h4>
            </div>
            <div id="reviewsContainer">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading reviews...</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Load reviews for this product
    loadProductReviews(product.product_id);
}

/**
 * Update quantity
 */
function updateQuantity(change) {
    const input = document.getElementById('productQuantity');
    let currentValue = parseInt(input.value) || 1;
    let newValue = currentValue + change;
    
    // Keep within bounds
    if (newValue < 1) newValue = 1;
    if (newValue > 99) newValue = 99;
    
    input.value = newValue;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Show vendor profile in modal
 */
function showVendorProfile(vendorId) {
    const modal = new bootstrap.Modal(document.getElementById('vendorModal'));
    const modalBody = document.getElementById('vendorModalBody');
    
    // Show loading
    modalBody.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading vendor profile...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch vendor profile and products
    Promise.all([
        fetch(`../actions/get_vendor_profile_action.php?vendor_id=${vendorId}`).then(res => res.json()),
        fetch(`../actions/view_user_products_action.php?vendor_id=${vendorId}`).then(res => res.json())
    ])
    .then(([vendorData, productsData]) => {
        if (vendorData.status === 'success') {
            const vendor = vendorData.vendor;
            const products = productsData.status === 'success' ? productsData.data : [];
            
            // Profile picture with UI Avatars fallback
            const vendorDisplayName = vendor.vendor_name || vendor.customer_name || 'Vendor';
            let profilePic = `https://ui-avatars.com/api/?name=${encodeURIComponent(vendorDisplayName)}&size=150&background=1e4d2b&color=fff&bold=true`;
            if (vendor.profile_picture) {
                if (vendor.profile_picture.startsWith('uploads/')) {
                    profilePic = '../' + vendor.profile_picture;
                } else if (vendor.profile_picture.startsWith('../uploads/')) {
                    profilePic = vendor.profile_picture;
                } else {
                    profilePic = vendor.profile_picture;
                }
            }
            
            const rating = parseFloat(vendor.average_rating || 0);
            const reviewCount = parseInt(vendor.rating_count || 0);
            
            modalBody.innerHTML = `
                <div class="vendor-modal-content">
                    <div class="vendor-modal-profile-header">
                        <img src="${profilePic}" alt="${escapeHtml(vendorDisplayName)}" 
                             onerror="if(!this.dataset.error){this.dataset.error='1';this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(vendorDisplayName)}&size=150&background=C9A961&color=fff&bold=true'}"
                             class="vendor-modal-profile-img">
                        <h3 class="vendor-modal-profile-name">${escapeHtml(vendorDisplayName)}</h3>
                        <p class="vendor-modal-profile-location">
                            <i class="bi bi-geo-alt me-1"></i>${escapeHtml(vendor.customer_city || 'Ghana')}
                        </p>
                        ${rating > 0 ? `
                        <div class="vendor-modal-profile-rating">
                            <i class="bi bi-star-fill" style="color: #FFD700;"></i>
                            <span style="color: white; font-weight: 600; margin-left: 5px;">${rating.toFixed(1)}</span>
                            <span style="color: rgba(255,255,255,0.8); font-size: 0.85rem; margin-left: 5px;">(${reviewCount} reviews)</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="vendor-modal-body">
                        ${vendor.about ? `
                        <div class="vendor-modal-section">
                            <h5 class="vendor-modal-section-title">
                                <i class="bi bi-info-circle me-2"></i>About
                            </h5>
                            <p class="vendor-modal-about">${escapeHtml(vendor.about)}</p>
                        </div>
                        ` : ''}
                        
                        <div class="vendor-modal-section">
                            <h5 class="vendor-modal-section-title">
                                <i class="bi bi-telephone me-2"></i>Contact Information
                            </h5>
                            <div class="vendor-modal-contact-row">
                                <div class="vendor-modal-contact-item">
                                    <div class="contact-label">Phone</div>
                                    <a href="tel:${vendor.customer_contact}" class="contact-link">
                                        <i class="bi bi-phone me-2"></i>${escapeHtml(vendor.customer_contact)}
                                    </a>
                                </div>
                                <div class="vendor-modal-contact-item">
                                    <div class="contact-label">Email</div>
                                    <a href="mailto:${vendor.customer_email}" class="contact-link">
                                        <i class="bi bi-envelope me-2"></i>${escapeHtml(vendor.customer_email)}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="vendor-modal-section">
                            <h5 class="vendor-modal-section-title">
                                <i class="bi bi-grid me-2"></i>Products & Services (${products.length})
                            </h5>
                            ${products.length > 0 ? `
                            <div class="vendor-modal-products">
                                ${products.slice(0, 6).map(product => {
                                    const imgUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
                                    const price = parseFloat(product.product_price).toFixed(2);
                                    return `
                                    <div class="vendor-modal-product-card" onclick="window.location.href='single_product.php?id=${product.product_id}'">
                                        <img src="${imgUrl}" alt="${escapeHtml(product.product_title)}" 
                                             onerror="this.src='../uploads/default-product.jpg'"
                                             class="vendor-modal-product-img">
                                        <div class="vendor-modal-product-info">
                                            <div class="vendor-modal-product-title">
                                                ${escapeHtml(product.product_title)}
                                            </div>
                                            <div class="vendor-modal-product-price">
                                                GHS ${price}
                                            </div>
                                        </div>
                                    </div>
                                    `;
                                }).join('')}
                            </div>
                            ${products.length > 6 ? `
                            <div class="vendor-modal-products-count">
                                And ${products.length - 6} more products...
                            </div>
                            ` : ''}
                            ` : `
                            <div class="vendor-modal-empty">
                                <i class="bi bi-box-seam vendor-modal-empty-icon"></i>
                                <p class="mt-2 mb-0">No products available yet</p>
                            </div>
                            `}
                        </div>
                    </div>
                </div>
            `;
        } else {
            modalBody.innerHTML = `
                <div class="text-center p-5">
                    <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">Failed to load vendor profile</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading vendor profile:', error);
        modalBody.innerHTML = `
            <div class="text-center p-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">An error occurred while loading the profile</p>
            </div>
        `;
    });
}

/**
 * Load package items for the product
 */
function loadPackageItems(productId) {
    fetch(`../actions/get_package_items_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.items && data.items.length > 0) {
                displayPackageItems(data.items);
            }
        })
        .catch(error => {
            console.error('Error loading package items:', error);
        });
}

/**
 * Display package items with checkboxes
 */
function displayPackageItems(items) {
    const container = document.getElementById('packageItemsContainer');
    if (!container) return;
    
    let html = '<div class="package-items-section">';
    html += '<div class="package-items-header">';
    html += '<i class="bi bi-check2-square"></i>';
    html += '<span>Customize Your Package</span>';
    html += '</div>';
    html += '<p class="package-items-subtitle">Select the items you want to include in your package:</p>';
    html += '<div class="package-items-list">';
    
    items.forEach(item => {
        const isOptional = item.is_optional == 1;
        const checkboxId = `packageItem_${item.item_id}`;
        
        html += `
            <div class="package-item-checkbox">
                <input 
                    type="checkbox" 
                    id="${checkboxId}" 
                    class="package-item-check" 
                    data-item-id="${item.item_id}"
                    data-item-name="${escapeHtml(item.item_name)}"
                    checked
                >
                <label for="${checkboxId}" class="package-item-label">
                    <div class="package-item-name">
                        ${escapeHtml(item.item_name)}
                    </div>
                </label>
            </div>
        `;
    });
    
    html += '</div></div>';
    container.innerHTML = html;
}

/**
 * Load reviews for a specific product
 */
function loadProductReviews(productId) {
    fetch(`../actions/get_product_reviews_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayProductReviews(data.reviews);
            } else {
                document.getElementById('reviewsContainer').innerHTML = 
                    '<div class="no-reviews"><i class="bi bi-chat-quote"></i><p>No reviews yet for this product</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            document.getElementById('reviewsContainer').innerHTML = 
                '<div class="alert alert-danger">Failed to load reviews</div>';
        });
}

/**
 * Display product reviews
 */
function displayProductReviews(reviews) {
    const container = document.getElementById('reviewsContainer');
    
    if (!reviews || reviews.length === 0) {
        container.innerHTML = '<div class="no-reviews"><i class="bi bi-chat-quote"></i><p>No reviews yet for this product</p></div>';
        return;
    }
    
    let html = '<div class="reviews-list">';
    reviews.forEach(review => {
        const rating = parseInt(review.rating);
        const stars = Array(5).fill(0).map((_, i) => 
            i < rating ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'
        ).join('');
        
        const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        html += `
            <div class="review-card">
                <div class="review-header">
                    <div class="review-author">
                        <div class="author-avatar">${escapeHtml(review.customer_name.charAt(0).toUpperCase())}</div>
                        <div>
                            <div class="author-name">${escapeHtml(review.customer_name)}</div>
                            <div class="review-date">${reviewDate}</div>
                        </div>
                    </div>
                    <div class="review-rating">${stars}</div>
                </div>
                ${review.review_text ? `
                <div class="review-body">
                    <p>${escapeHtml(review.review_text)}</p>
                </div>
                ` : ''}
                <div class="review-footer">
                    <span class="review-badge"><i class="bi bi-patch-check-fill me-1"></i>Verified Purchase</span>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

/**
 * Toggle wishlist (add/remove)
 */
function toggleWishlist(productId) {
    const btn = document.getElementById('wishlistBtn');
    const icon = btn.querySelector('i');
    const isFilled = icon.classList.contains('bi-heart-fill');
    
    if (isFilled) {
        // Remove from wishlist
        removeFromWishlist(productId, btn, icon);
    } else {
        // Add to wishlist
        addToWishlist(productId, btn, icon);
    }
}

/**
 * Add product to wishlist
 */
function addToWishlist(productId, btn, icon) {
    btn.disabled = true;
    
    fetch('../actions/add_to_wishlist_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Update button to "filled" state
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
            btn.style.background = 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)';
            btn.innerHTML = '<i class="bi bi-heart-fill me-2"></i>In Wishlist';
            
            showWishlistAlert('success', data.message);
            updateWishlistCount(data.wishlist_count);
        } else {
            showWishlistAlert('error', data.message);
        }
        btn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        showWishlistAlert('error', 'Failed to add to wishlist');
        btn.disabled = false;
    });
}

/**
 * Remove product from wishlist
 */
function removeFromWishlist(productId, btn, icon) {
    btn.disabled = true;
    
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
            // Update button to "unfilled" state
            icon.classList.remove('bi-heart-fill');
            icon.classList.add('bi-heart');
            btn.style.background = '';
            btn.innerHTML = '<i class="bi bi-heart me-2"></i>Add to Wishlist';
            
            showWishlistAlert('success', data.message);
            updateWishlistCount(data.wishlist_count);
        } else {
            showWishlistAlert('error', data.message);
        }
        btn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        showWishlistAlert('error', 'Failed to remove from wishlist');
        btn.disabled = false;
    });
}

/**
 * Show alert message for wishlist actions
 */
function showWishlistAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="bi ${iconClass} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = alertHtml;
    document.body.appendChild(tempDiv.firstElementChild);
    
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 3000);
}

/**
 * Update wishlist count in header
 */
function updateWishlistCount(count) {
    const wishlistBadge = document.querySelector('.wishlist-count');
    if (wishlistBadge) {
        wishlistBadge.textContent = count;
        if (count > 0) {
            wishlistBadge.style.display = 'inline-block';
        } else {
            wishlistBadge.style.display = 'none';
        }
    }
}