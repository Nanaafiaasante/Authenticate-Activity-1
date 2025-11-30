/**
 * Product Search Results JavaScript
 * Handles search results display and filtering
 */

// Global state
let currentPage = 1;
let perPage = 10;
let currentFilters = {
    query: initialSearchQuery || '',
    category: '',
    brand: '',
    sortBy: 'relevance'
};
let categories = [];
let brands = [];
let userLocation = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadFilterOptions();
    loadSearchResults();
    
    // Event listeners
    document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
    document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);
    
    const sortElement = document.getElementById('sortFilter');
    if (sortElement) {
        sortElement.addEventListener('change', applyFilters);
    }
});

/**
 * Load filter options (categories and brands)
 */
function loadFilterOptions() {
    fetch('../actions/get_filter_options_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // API returns categories and brands directly, not nested in data.data
                categories = data.categories || [];
                brands = data.brands || [];
                populateFilterDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading filter options:', error);
        });
}

/**
 * Populate filter dropdowns
 */
function populateFilterDropdowns() {
    const categorySelect = document.getElementById('categoryFilter');
    const brandSelect = document.getElementById('brandFilter');
    
    // Clear existing options (except first)
    categorySelect.innerHTML = '<option value="">All Categories</option>';
    brandSelect.innerHTML = '<option value="">All Brands</option>';
    
    // Populate categories
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.cat_id;
        option.textContent = category.cat_name;
        categorySelect.appendChild(option);
    });
    
    // Populate brands
    brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand.brand_id;
        option.textContent = brand.brand_name;
        brandSelect.appendChild(option);
    });
}

/**
 * Load search results
 */
function loadSearchResults(page = 1) {
    currentPage = page;
    const container = document.getElementById('productsContainer');
    const statsContainer = document.getElementById('searchStats');
    
    // Show loading
    container.innerHTML = `
        <div class="col-12">
            <div class="loading-spinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading products...</p>
            </div>
        </div>
    `;
    
    // Build query string
    let queryString = `page=${page}&per_page=${perPage}`;
    
    // If we have filters, use composite filter
    if (currentFilters.category || currentFilters.brand) {
        queryString += `&type=composite&query=${encodeURIComponent(currentFilters.query)}`;
        if (currentFilters.category) queryString += `&category=${currentFilters.category}`;
        if (currentFilters.brand) queryString += `&brand=${currentFilters.brand}`;
        
        fetch(`../actions/filter_products_action.php?${queryString}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displaySearchResults(data.data, data.total, data.total_pages);
                    updateSearchStats(data.total);
                } else {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>${data.message || 'No products found'}
                            </div>
                        </div>
                    `;
                    if (statsContainer) {
                        statsContainer.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>An error occurred while loading products
                        </div>
                    </div>
                `;
            });
    } else {
        // Just search query
        queryString += `&query=${encodeURIComponent(currentFilters.query)}`;
        
        fetch(`../actions/search_products_action.php?${queryString}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displaySearchResults(data.data, data.total, data.total_pages);
                    updateSearchStats(data.total);
                } else {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>${data.message || 'No products found'}
                            </div>
                        </div>
                    `;
                    if (statsContainer) {
                        statsContainer.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle me-2"></i>An error occurred while loading products
                        </div>
                    </div>
                `;
            });
    }
}

/**
 * Display search results
 */
function displaySearchResults(products, total, totalPages) {
    const container = document.getElementById('productsContainer');
    
    if (!products || products.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No products found matching your search criteria.
                    <div class="mt-3">
                        <button class="btn btn-primary-custom" onclick="clearFilters()">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('paginationContainer').innerHTML = '';
        return;
    }
    
    // Apply sorting if needed
    const sortBy = currentFilters.sortBy;
    if (sortBy === 'newest') {
        products.sort((a, b) => b.product_id - a.product_id);
    } else if (sortBy === 'price_low') {
        products.sort((a, b) => parseFloat(a.product_price) - parseFloat(b.product_price));
    } else if (sortBy === 'price_high') {
        products.sort((a, b) => parseFloat(b.product_price) - parseFloat(a.product_price));
    }
    
    // Get user location and display products with distance
    getUserLocation().then(userLoc => {
        let html = '<div class="row g-4">';
        
        products.forEach(product => {
            const imageUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
            const price = parseFloat(product.product_price).toFixed(2);
            
            // Calculate distance if both user location and vendor location are available
            let distanceInfo = '';
            if (userLoc && product.vendor_latitude && product.vendor_longitude) {
                const distance = calculateDistance(
                    userLoc.latitude,
                    userLoc.longitude,
                    parseFloat(product.vendor_latitude),
                    parseFloat(product.vendor_longitude)
                );
                distanceInfo = `
                    <p class="product-distance">
                        <i class="bi bi-pin-map-fill"></i> ${formatDistance(distance)}
                    </p>
                `;
            } else if (product.vendor_city) {
                distanceInfo = `
                    <p class="product-location">
                        <i class="bi bi-geo-alt"></i> ${escapeHtml(product.vendor_city)}
                    </p>
                `;
            }
            
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="product-card" onclick="window.location.href='single_product.php?id=${product.product_id}'" style="cursor: pointer;">
                        <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" 
                             class="product-image" onerror="this.src='../uploads/default-product.jpg'">
                        <div class="product-body">
                            <h5 class="product-title">${escapeHtml(product.product_title)}</h5>
                            <p class="product-vendor">
                                <i class="bi bi-person"></i> by <a href="vendor_profile.php?id=${product.vendor_customer_id}" onclick="event.stopPropagation();" style="color: #C9A961; text-decoration: none; font-weight: 600;">${escapeHtml(product.vendor_name || 'Unknown Vendor')}</a>
                            </p>
                            ${distanceInfo}
                            <div class="product-price">GHS ${price}</div>
                            <div class="product-badges">
                                <span class="badge-custom">${escapeHtml(product.cat_name || 'Uncategorized')}</span>
                                <span class="badge-custom badge-brand">${escapeHtml(product.brand_name || 'No Brand')}</span>
                            </div>
                            <div id="packageItems${product.product_id}" class="package-items-preview"></div>
                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="event.stopPropagation(); addToCart(${product.product_id}, '${escapeHtml(product.product_title).replace(/'/g, "\\'")}')">
                                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                </button>
                                <button class="btn-wishlist-icon" data-product-id="${product.product_id}" onclick="event.stopPropagation(); toggleWishlist(${product.product_id}, this);" title="Add to Wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        
        // Load package items for all products after DOM is ready
        products.forEach(product => {
            loadProductPackageItems(product.product_id);
        });
        
        renderPagination(totalPages, currentPage);
    });
}

/**
 * Add to cart placeholder function
 */
function addToCart(productId, productTitle) {
    alert(`"${productTitle}" added to cart!\n(Cart functionality will be implemented in future labs)`);
}

/**
 * Update search stats
 */
function updateSearchStats(total) {
    const statsContainer = document.getElementById('searchStats');
    const resultsCount = document.getElementById('resultsCount');
    
    // Update the results count if the element exists
    if (resultsCount) {
        resultsCount.textContent = total;
    }
    
    // Show the stats container
    if (statsContainer) {
        statsContainer.style.display = 'block';
    }
}

/**
 * Apply filters
 */
function applyFilters() {
    currentFilters.category = document.getElementById('categoryFilter').value;
    currentFilters.brand = document.getElementById('brandFilter').value;
    
    // Use correct element ID - it's 'sortFilter' in the HTML
    const sortElement = document.getElementById('sortFilter');
    currentFilters.sortBy = sortElement ? sortElement.value : 'relevance';
    
    loadSearchResults(1);
}

/**
 * Clear filters
 */
function clearFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('brandFilter').value = '';
    
    // Use correct element ID - it's 'sortFilter' in the HTML
    const sortElement = document.getElementById('sortFilter');
    if (sortElement) {
        sortElement.value = 'relevance';
    }
    
    currentFilters.category = '';
    currentFilters.brand = '';
    currentFilters.sortBy = 'relevance';
    
    loadSearchResults(1);
}

/**
 * Get user's current location
 */
function getUserLocation() {
    return new Promise((resolve) => {
        if (!navigator.geolocation) {
            console.log('Geolocation not supported');
            resolve(null);
            return;
        }

        if (userLocation) {
            resolve(userLocation);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };
                resolve(userLocation);
            },
            (error) => {
                console.log('Error getting location:', error);
                resolve(null);
            },
            { timeout: 5000, maximumAge: 300000 }
        );
    });
}

/**
 * Calculate distance between two coordinates using Haversine formula
 */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // Distance in kilometers
}

/**
 * Convert degrees to radians
 */
function toRadians(degrees) {
    return degrees * (Math.PI / 180);
}

/**
 * Format distance for display
 */
function formatDistance(distance) {
    if (distance < 1) {
        return `${Math.round(distance * 1000)}m away`;
    }
    return `${distance.toFixed(1)}km away`;
}

/**
 * Load package items for a product
 */
function loadProductPackageItems(productId) {
    fetch(`../actions/get_package_items_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Package items response:', data); // Debug log
            if (data.status === 'success' && data.items && data.items.length > 0) {
                displayPackageItemsPreview(productId, data.items);
            }
        })
        .catch(error => {
            console.error('Error loading package items:', error);
        });
}

/**
 * Display package items preview in product card
 */
function displayPackageItemsPreview(productId, items) {
    const container = document.getElementById(`packageItems${productId}`);
    if (!container || !items || items.length === 0) return;
    
    const maxPreview = 3;
    const displayItems = items.slice(0, maxPreview);
    const remaining = items.length - maxPreview;
    
    let html = `
        <div class="package-preview-section">
            <div class="package-preview-title">
                <i class="bi bi-box-seam"></i> Includes (click to customize)
            </div>
    `;
    
    displayItems.forEach(item => {
        html += `
            <div class="package-preview-item">
                <i class="bi bi-check-circle-fill"></i> ${escapeHtml(item.item_name)}
            </div>
        `;
    });
    
    if (remaining > 0) {
        html += `
            <div class="package-preview-more">
                +${remaining} more item${remaining > 1 ? 's' : ''}
            </div>
        `;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Toggle wishlist for product
 */
function toggleWishlist(productId, button) {
    // Check if user is logged in
    fetch('../actions/add_to_wishlist_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            if (icon.classList.contains('bi-heart-fill')) {
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                showToast('Removed from wishlist', 'success');
            } else {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                showToast('Added to wishlist!', 'success');
            }
        } else {
            if (data.message && data.message.includes('login')) {
                window.location.href = '../login/login.php';
            } else {
                showToast(data.message || 'Error updating wishlist', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating wishlist', 'error');
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#2d5a3a' : '#dc3545'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Render pagination
 */
function renderPagination(totalPages, current) {
    const container = document.getElementById('paginationContainer');
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSearchResults(${currentPage - 1}); return false;">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
    `;
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadSearchResults(1); return false;">1</a>
            </li>
        `;
        if (startPage > 2) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadSearchResults(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        html += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadSearchResults(${totalPages}); return false;">${totalPages}</a>
            </li>
        `;
    }
    
    // Next button
    html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadSearchResults(${currentPage + 1}); return false;">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `;
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
