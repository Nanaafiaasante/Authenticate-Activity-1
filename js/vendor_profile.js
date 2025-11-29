/**
 * Vendor Profile JavaScript
 * Handles vendor profile display, editing, and rating system
 */

document.addEventListener('DOMContentLoaded', function() {
    loadVendorProfile();
    setupEventListeners();
});

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Profile picture preview
    const profilePicInput = document.getElementById('profilePicture');
    if (profilePicInput) {
        profilePicInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Save profile button
    const saveBtn = document.getElementById('saveProfileBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', saveProfile);
    }
}

/**
 * Load vendor profile data
 */
function loadVendorProfile() {
    fetch(`../actions/get_vendor_profile_action.php?vendor_id=${vendorId}`)
        .then(response => {
            // Check if response is OK
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            // Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                displayProfile(data.vendor);
            } else {
                showError('Failed to load profile: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading profile:', error);
            showError('Failed to load profile. Please check if the vendor exists and try again.');
        });
}

/**
 * Display vendor profile
 */
function displayProfile(vendor) {
    const container = document.getElementById('profileContainer');
    
    const vendorDisplayName = vendor.vendor_name || vendor.customer_name || 'Vendor';
    
    // Handle profile picture path - use UI Avatars as default
    let profilePicture = `https://ui-avatars.com/api/?name=${encodeURIComponent(vendorDisplayName)}&size=150&background=1e4d2b&color=fff&bold=true`;
    if (vendor.profile_picture) {
        // If path starts with 'uploads/', add '../' prefix
        if (vendor.profile_picture.startsWith('uploads/')) {
            profilePicture = '../' + vendor.profile_picture;
        } else if (vendor.profile_picture.startsWith('../uploads/')) {
            profilePicture = vendor.profile_picture;
        } else {
            // Assume it's a full path from root
            profilePicture = vendor.profile_picture;
        }
    }
    
    const vendorName = vendor.vendor_name || vendor.customer_name || 'Vendor';
    const averageRating = parseFloat(vendor.average_rating || 0);
    const ratingCount = parseInt(vendor.rating_count || 0);
    const subscriptionTier = vendor.subscription_tier || 'basic';
    const subscriptionStatus = vendor.subscription_status || 'pending';
    
    // Generate stars
    const starsHtml = generateStars(averageRating);
    
    // Subscription badge
    const subscriptionBadgeClass = subscriptionTier === 'premium' ? 'premium' : 'basic';
    const subscriptionIcon = subscriptionTier === 'premium' ? 'bi-gem' : 'bi-shield-check';
    const subscriptionText = subscriptionTier === 'premium' ? 'Premium Plan' : 'Basic Plan';
    
    const html = `
        <div class="profile-header-card">
            <div class="profile-cover">
                ${isOwnProfile ? `
                    <button class="edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Profile</span>
                    </button>
                ` : ''}
            </div>
            <div class="profile-header-content">
                <div class="profile-picture-wrapper">
                    <img src="${profilePicture}" alt="${escapeHtml(vendorName)}" class="profile-picture" onerror="if(!this.dataset.error){this.dataset.error='1';this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(vendorName)}&size=150&background=1e4d2b&color=fff&bold=true'}">
                </div>
                
                <h1 class="profile-name">${escapeHtml(vendor.customer_name)}</h1>
                <div class="profile-vendor-name">${escapeHtml(vendorName)}</div>
                
                <div class="rating-display">
                    <div class="stars">${starsHtml}</div>
                    <span class="rating-text">${averageRating.toFixed(1)}</span>
                    <span class="rating-count">(${ratingCount} ${ratingCount === 1 ? 'review' : 'reviews'})</span>
                </div>
                
                <div class="profile-info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Phone</div>
                            <div class="info-value">${vendor.customer_contact || 'Not provided'}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Location</div>
                            <div class="info-value">${vendor.customer_city || 'Not provided'}, ${vendor.customer_country || ''}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email</div>
                            <div class="info-value">${vendor.customer_email || 'Not provided'}</div>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Subscription</div>
                            <div class="info-value">
                                <span class="subscription-badge ${subscriptionBadgeClass}">
                                    <i class="bi ${subscriptionIcon}"></i>
                                    ${subscriptionText}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="about-section">
            <h3 class="section-title">
                <i class="bi bi-info-circle"></i>
                About
            </h3>
            <div class="about-text">
                ${vendor.about ? escapeHtml(vendor.about) : '<span class="about-empty">No description provided yet.</span>'}
            </div>
        </div>
        
        <div class="reviews-section">
            <h3 class="section-title">
                <i class="bi bi-chat-square-quote"></i>
                Customer Reviews
            </h3>
            <div id="vendorReviewsContainer">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" style="color: #1e4d2b;" role="status"></div>
                    <small class="d-block mt-2">Loading reviews...</small>
                </div>
            </div>
        </div>
        
        <div class="products-section">
            <h3 class="section-title">
                <i class="bi bi-grid"></i>
                Products & Services
            </h3>
            <div id="vendorProductsContainer">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm" style="color: #1e4d2b;" role="status"></div>
                    <small class="d-block mt-2">Loading products...</small>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Load vendor reviews
    loadVendorReviews(vendor.customer_id);
    
    // Load vendor products
    loadVendorProducts(vendor.customer_id);
    
    // Pre-fill edit form if own profile
    if (isOwnProfile) {
        populateEditForm(vendor);
    }
}

/**
 * Generate star rating HTML
 */
function generateStars(rating) {
    let html = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        html += '<i class="bi bi-star-fill star filled"></i>';
    }
    
    // Half star
    if (hasHalfStar) {
        html += '<i class="bi bi-star-half star half-filled"></i>';
    }
    
    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        html += '<i class="bi bi-star star"></i>';
    }
    
    return html;
}

/**
 * Load vendor reviews
 */
function loadVendorReviews(vendorId) {
    fetch(`../actions/get_vendor_reviews_action.php?vendor_id=${vendorId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.reviews && data.reviews.length > 0) {
                displayReviews(data.reviews);
            } else {
                document.getElementById('vendorReviewsContainer').innerHTML = `
                    <div class="empty-reviews">
                        <i class="bi bi-chat-quote"></i>
                        <p>No reviews yet</p>
                        <small class="text-muted">Be the first to review this vendor!</small>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading reviews:', error);
            document.getElementById('vendorReviewsContainer').innerHTML = `
                <div class="empty-reviews">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>Failed to load reviews</p>
                </div>
            `;
        });
}

/**
 * Display vendor reviews
 */
function displayReviews(reviews) {
    const container = document.getElementById('vendorReviewsContainer');
    
    let html = '<div class="reviews-list">';
    reviews.forEach(review => {
        const reviewDate = new Date(review.order_date).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        const customerImage = review.customer_image 
            ? `../${review.customer_image}` 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(review.customer_name)}&size=60&background=1e4d2b&color=fff&bold=true`;
        
        html += `
            <div class="review-card">
                <div class="review-header">
                    <img src="${customerImage}" alt="${escapeHtml(review.customer_name)}" class="review-avatar" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(review.customer_name)}&size=60&background=1e4d2b&color=fff&bold=true'">
                    <div class="review-header-info">
                        <div class="review-customer-name">${escapeHtml(review.customer_name)}</div>
                        <div class="review-date">${reviewDate}</div>
                    </div>
                    <div class="review-rating">
                        ${generateStars(review.rating)}
                    </div>
                </div>
                ${review.review_comment ? `
                    <div class="review-comment">
                        "${escapeHtml(review.review_comment)}"
                    </div>
                ` : ''}
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

/**
 * Load vendor products
 */
function loadVendorProducts(vendorId) {
    fetch(`../actions/view_user_products_action.php?vendor_id=${vendorId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Products response:', data);
            if (data.status === 'success' && data.data && data.data.length > 0) {
                displayProducts(data.data);
            } else {
                document.getElementById('vendorProductsContainer').innerHTML = `
                    <div class="empty-products">
                        <i class="bi bi-box-seam"></i>
                        <p>No products available yet</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('vendorProductsContainer').innerHTML = `
                <div class="empty-products">
                    <i class="bi bi-exclamation-circle"></i>
                    <p>Failed to load products</p>
                </div>
            `;
        });
}

/**
 * Display vendor products
 */
function displayProducts(products) {
    const container = document.getElementById('vendorProductsContainer');
    
    let html = '<div class="products-grid">';
    products.forEach(product => {
        const imageUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
        const price = parseFloat(product.product_price).toFixed(2);
        
        html += `
            <div class="product-card-mini" onclick="window.location.href='single_product.php?id=${product.product_id}'">
                <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" onerror="if(!this.dataset.error){this.dataset.error='1';this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22250%22 height=%22180%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22250%22 height=%22180%22/%3E%3Ctext fill=%22%23999%22 font-family=%22Arial%22 font-size=%2218%22 text-anchor=%22middle%22 x=%22125%22 y=%2295%22%3ENo Image%3C/text%3E%3C/svg%3E'}">
                <div class="product-card-mini-body">
                    <div class="product-card-mini-title">${escapeHtml(product.product_title)}</div>
                    <div class="product-card-mini-price">GHS ${price}</div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

/**
 * Populate edit form with current data
 */
function populateEditForm(vendor) {
    const vendorDisplayName = vendor.vendor_name || vendor.customer_name || 'Vendor';
    
    // Handle profile picture path - use UI Avatars as default
    let profilePictureSrc = `https://ui-avatars.com/api/?name=${encodeURIComponent(vendorDisplayName)}&size=150&background=1e4d2b&color=fff&bold=true`;
    if (vendor.profile_picture) {
        if (vendor.profile_picture.startsWith('uploads/')) {
            profilePictureSrc = '../' + vendor.profile_picture;
        } else if (vendor.profile_picture.startsWith('../uploads/')) {
            profilePictureSrc = vendor.profile_picture;
        } else {
            profilePictureSrc = vendor.profile_picture;
        }
    }
    
    document.getElementById('previewImage').src = profilePictureSrc;
    document.getElementById('vendorName').value = vendor.vendor_name || '';
    document.getElementById('fullName').value = vendor.customer_name || '';
    document.getElementById('phoneNumber').value = vendor.customer_contact || '';
    document.getElementById('city').value = vendor.customer_city || '';
    document.getElementById('country').value = vendor.customer_country || '';
    document.getElementById('about').value = vendor.about || '';
}

/**
 * Save profile changes
 */
function saveProfile() {
    const formData = new FormData();
    formData.append('vendor_id', vendorId);
    formData.append('vendor_name', document.getElementById('vendorName').value.trim());
    formData.append('city', document.getElementById('city').value.trim());
    formData.append('country', document.getElementById('country').value.trim());
    formData.append('about', document.getElementById('about').value.trim());
    
    // Add profile picture if changed
    const profilePicInput = document.getElementById('profilePicture');
    if (profilePicInput.files.length > 0) {
        formData.append('profile_picture', profilePicInput.files[0]);
    }
    
    // Show loading
    const saveBtn = document.getElementById('saveProfileBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    fetch('../actions/update_vendor_profile_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: 'Your profile has been updated successfully.',
                confirmButtonColor: '#1e4d2b'
            }).then(() => {
                // Close modal and reload profile
                bootstrap.Modal.getInstance(document.getElementById('editProfileModal')).hide();
                loadVendorProfile();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message || 'Failed to update profile. Please try again.',
                confirmButtonColor: '#1e4d2b'
            });
        }
    })
    .catch(error => {
        console.error('Error saving profile:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.',
            confirmButtonColor: '#1e4d2b'
        });
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Changes';
    });
}

/**
 * Show error message
 */
function showError(message) {
    document.getElementById('profileContainer').innerHTML = `
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>${message}
        </div>
    `;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
