/**
 * All Products JavaScript
 * Handles product display, filtering, searching, and pagination
 */

// Global variables
let currentPage = 1;
let perPage = 10;
let currentFilters = {};
let allProducts = [];
let categories = [];
let brands = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load filter options first, then handle initial load based on URL params
    loadFilterOptions().then(() => {
        handleInitialLoad();
    });
    
    // Event listeners
    document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
    document.getElementById('clearFiltersBtn').addEventListener('click', clearFilters);
    document.getElementById('searchBtn').addEventListener('click', performSearch);
    document.getElementById('sortFilter').addEventListener('change', applyFilters);
    
    // Enter key for search
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});

/**
 * Load filter options (categories and brands)
 */
function loadFilterOptions() {
    return fetch('../actions/get_filter_options_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Support both {data: {categories, brands}} and flat {categories, brands}
                const payload = data.data || data;
                categories = payload.categories || [];
                brands = payload.brands || [];
                populateFilterDropdowns();
            }
        })
        .catch(error => {
            console.error('Error loading filter options:', error);
        });
}

/**
 * Handle initial load based on URL query params
 */
function handleInitialLoad() {
    const params = new URLSearchParams(window.location.search);
    const category = params.get('category') || '';
    const brand = params.get('brand') || '';
    const minPrice = params.get('min_price') || '';
    const maxPrice = params.get('max_price') || '';
    const search = params.get('search') || '';
    const sort = params.get('sort') || '';

    // Set form values if present
    if (category) document.getElementById('categoryFilter').value = category;
    if (brand) document.getElementById('brandFilter').value = brand;
    if (minPrice) document.getElementById('minPrice').value = minPrice;
    if (maxPrice) document.getElementById('maxPrice').value = maxPrice;
    if (search) document.getElementById('searchInput').value = search;
    if (sort) document.getElementById('sortFilter').value = sort;

    // If any filter is provided via URL, apply filters; else load default products
    if (category || brand || minPrice || maxPrice || search) {
        applyFilters();
    } else {
        loadProducts();
    }
}

/**
 * Populate filter dropdowns
 */
function populateFilterDropdowns() {
    const categorySelect = document.getElementById('categoryFilter');
    const brandSelect = document.getElementById('brandFilter');
    
    // Populate categories
    categorySelect.innerHTML = '<option value="">All Categories</option>';
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.cat_id;
        option.textContent = category.cat_name;
        categorySelect.appendChild(option);
    });
    
    // Populate brands
    brandSelect.innerHTML = '<option value="">All Brands</option>';
    brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand.brand_id;
        option.textContent = brand.brand_name;
        brandSelect.appendChild(option);
    });
}

/**
 * Load products
 */
function loadProducts(page = 1) {
    currentPage = page;
    const container = document.getElementById('productsContainer');
    
    container.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading products...</p>
        </div>
    `;
    
    fetch(`../actions/view_all_products_action.php?page=${page}&per_page=${perPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                allProducts = data.data || [];
                displayProducts(allProducts);
                renderPagination(data.total_pages, page);
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>An error occurred while loading products
                </div>
            `;
        });
}

// User's current location
let userLocation = null;

/**
 * Use mock location for testing (Accra, Ghana coordinates)
 */
function useMockLocation() {
    userLocation = {
        latitude: 5.6037,
        longitude: -0.1870,
        isMock: true
    };
    
    updateLocationStatus('Using test location (Accra, Ghana)');
    
    // Reload products to show distances
    if (Object.keys(currentFilters).length > 0) {
        applyFilters();
    } else {
        loadProducts(currentPage);
    }
}

/**
 * Use real user location from browser
 */
function useRealLocation() {
    userLocation = null; // Reset to trigger new geolocation request
    
    if ('geolocation' in navigator) {
        updateLocationStatus('Getting your location...');
        
        navigator.geolocation.getCurrentPosition(
            position => {
                userLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    isMock: false
                };
                updateLocationStatus('Using your actual location');
                
                // Reload products to show distances
                if (Object.keys(currentFilters).length > 0) {
                    applyFilters();
                } else {
                    loadProducts(currentPage);
                }
            },
            error => {
                updateLocationStatus('Location access denied. Using test location.');
                useMockLocation();
            },
            { timeout: 5000 }
        );
    } else {
        updateLocationStatus('Geolocation not supported. Using test location.');
        useMockLocation();
    }
}

/**
 * Update location status message
 */
function updateLocationStatus(message) {
    const statusEl = document.getElementById('locationStatus');
    if (statusEl) {
        statusEl.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${message}`;
    }
}

/**
 * Get user's current location
 */
function getUserLocation() {
    return new Promise((resolve, reject) => {
        if (userLocation) {
            resolve(userLocation);
            return;
        }
        
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                position => {
                    userLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        isMock: false
                    };
                    resolve(userLocation);
                },
                error => {
                    console.log('Geolocation not available:', error.message);
                    resolve(null);
                },
                { timeout: 5000, maximumAge: 300000 } // 5 min cache
            );
        } else {
            resolve(null);
        }
    });
}

/**
 * Calculate distance between two coordinates using Haversine formula
 * @param {number} lat1 - Latitude of point 1
 * @param {number} lon1 - Longitude of point 1
 * @param {number} lat2 - Latitude of point 2
 * @param {number} lon2 - Longitude of point 2
 * @returns {number} - Distance in kilometers
 */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);
    
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;
    
    return distance;
}

/**
 * Convert degrees to radians
 */
function toRadians(degrees) {
    return degrees * (Math.PI / 180);
}

/**
 * Format distance for display
 * @param {number} distance - Distance in kilometers
 * @returns {string} - Formatted distance string
 */
function formatDistance(distance) {
    if (distance < 1) {
        return `${Math.round(distance * 1000)}m away`;
    } else if (distance < 10) {
        return `${distance.toFixed(1)}km away`;
    } else {
        return `${Math.round(distance)}km away`;
    }
}

/**
 * Display products in grid
 */
function displayProducts(products) {
    const container = document.getElementById('productsContainer');
    const resultsCount = document.getElementById('resultsCount');
    
    // Update results count
    if (resultsCount) {
        const count = products.length;
        resultsCount.textContent = `${count} ${count === 1 ? 'product' : 'products'} found`;
    }
    
    if (products.length === 0) {
        container.innerHTML = `
            <div class="no-products">
                <i class="bi bi-inbox"></i>
                <h3>No Products Found</h3>
                <p>Try adjusting your filters or browse all products.</p>
                <button class="btn-filter" onclick="clearFilters()">
                    Clear Filters
                </button>
            </div>
        `;
        return;
    }
    
    // Try to get user location and display products
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
                        <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" class="product-image" 
                             onerror="this.src='../uploads/default-product.jpg'">
                        <div class="product-body">
                            <h5 class="product-title">${escapeHtml(product.product_title)}</h5>
                            <p class="product-vendor">
                                <i class="bi bi-person"></i> by <a href="vendor_profile.php?id=${product.vendor_customer_id}" onclick="event.stopPropagation();" style="color: #C9A961; text-decoration: none; font-weight: 600;">${escapeHtml(product.vendor_name)}</a>
                            </p>
                            ${distanceInfo}
                            <div class="product-price">GHS ${price}</div>
                            <div class="product-badges">
                                <span class="badge-custom">${escapeHtml(product.cat_name)}</span>
                                <span class="badge-custom badge-brand">${escapeHtml(product.brand_name)}</span>
                            </div>
                            <div id="packageItems${product.product_id}" class="package-items-preview"></div>
                            <div class="product-actions">
                                <button class="btn-add-cart add-to-cart-btn" data-product-id="${product.product_id}" data-quantity="1" onclick="event.stopPropagation(); addToCartFromProducts(${product.product_id}, '${escapeHtml(product.product_title)}');">
                                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                </button>
                                <button class="btn-wishlist-icon" data-product-id="${product.product_id}" onclick="event.stopPropagation(); toggleWishlistFromProducts(${product.product_id}, this);" title="Add to Wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Load package items for this product
            loadProductPackageItems(product.product_id);
        });
        
        html += '</div>';
        container.innerHTML = html;
    });
}

/**
 * Add to cart function (actual implementation)
 */
function addToCartFromProducts(productId, productTitle) {
    // Show loading message
    if (typeof showLoadingMessage === 'function') {
        showLoadingMessage('Adding to cart...');
    }
    
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (typeof showSuccessMessage === 'function') {
                showSuccessMessage(data.message);
            } else {
                alert(data.message);
            }
            
            // Update cart count if function exists
            if (typeof updateCartCount === 'function') {
                updateCartCount(data.cart_count);
            }
        } else {
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(data.message);
            } else {
                alert(data.message);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showErrorMessage === 'function') {
            showErrorMessage('Failed to add item to cart. Please try again.');
        } else {
            alert('Failed to add item to cart. Please try again.');
        }
    });
}

/**
 * Apply filters
 */
function applyFilters() {
    const minPriceInput = document.getElementById('minPrice').value;
    const maxPriceInput = document.getElementById('maxPrice').value;
    
    // Validate price range
    const minPrice = minPriceInput ? parseFloat(minPriceInput) : null;
    const maxPrice = maxPriceInput ? parseFloat(maxPriceInput) : null;
    
    if (minPrice !== null && maxPrice !== null && minPrice > maxPrice) {
        alert('Minimum price cannot be greater than maximum price!');
        return;
    }
    
    const filters = {
        category: document.getElementById('categoryFilter').value,
        brand: document.getElementById('brandFilter').value,
        min_price: minPrice,
        max_price: maxPrice,
        search: document.getElementById('searchInput').value,
        sort: document.getElementById('sortFilter').value
    };
    
    currentFilters = filters;
    currentPage = 1; // Reset to first page when applying filters
    
    // Build query string
    let queryString = 'type=composite';
    
    if (filters.category) queryString += `&category=${filters.category}`;
    if (filters.brand) queryString += `&brand=${filters.brand}`;
    if (filters.min_price !== null) queryString += `&min_price=${filters.min_price}`;
    if (filters.max_price !== null) queryString += `&max_price=${filters.max_price}`;
    if (filters.search) queryString += `&search=${encodeURIComponent(filters.search)}`;
    if (filters.sort) queryString += `&sort=${filters.sort}`;
    
    queryString += `&page=${currentPage}&per_page=${perPage}`;
    
    const container = document.getElementById('productsContainer');
    container.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Applying filters...</p>
        </div>
    `;
    
    fetch(`../actions/filter_products_action.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayProducts(data.data || []);
                renderPagination(data.total_pages, data.page);
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error filtering products:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>An error occurred while filtering products
                </div>
            `;
        });
}

/**
 * Clear all filters
 */
function clearFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('brandFilter').value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('sortFilter').value = 'newest';
    
    currentFilters = {};
    currentPage = 1;
    
    loadProducts(1);
}

/**
 * Perform search
 */
function performSearch() {
    const query = document.getElementById('searchInput').value.trim();
    
    if (query) {
        window.location.href = `product_search_result.php?query=${encodeURIComponent(query)}`;
    } else {
        loadProducts(1);
    }
}

/**
 * Render pagination
 */
function renderPagination(totalPages, currentPageNum) {
    const container = document.getElementById('paginationContainer');
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<ul class="pagination">';
    
    // Determine if we're using filters
    const hasFilters = Object.keys(currentFilters).length > 0;
    const clickHandler = hasFilters ? 'loadFilteredPage' : 'loadProducts';
    
    // Previous button
    if (currentPageNum > 1) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="${clickHandler}(${currentPageNum - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                 </li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPageNum - 2 && i <= currentPageNum + 2)) {
            const activeClass = i === currentPageNum ? 'active' : '';
            html += `<li class="page-item ${activeClass}">
                        <a class="page-link" href="#" onclick="${clickHandler}(${i}); return false;">${i}</a>
                     </li>`;
        } else if (i === currentPageNum - 3 || i === currentPageNum + 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Next button
    if (currentPageNum < totalPages) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="${clickHandler}(${currentPageNum + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                 </li>`;
    }
    
    html += '</ul>';
    container.innerHTML = html;
}

/**
 * Load a specific page with current filters applied
 */
function loadFilteredPage(page) {
    currentPage = page;
    
    // Build query string with current filters
    let queryString = 'type=composite';
    
    if (currentFilters.category) queryString += `&category=${currentFilters.category}`;
    if (currentFilters.brand) queryString += `&brand=${currentFilters.brand}`;
    if (currentFilters.min_price !== null && currentFilters.min_price !== undefined) queryString += `&min_price=${currentFilters.min_price}`;
    if (currentFilters.max_price !== null && currentFilters.max_price !== undefined) queryString += `&max_price=${currentFilters.max_price}`;
    if (currentFilters.search) queryString += `&search=${encodeURIComponent(currentFilters.search)}`;
    if (currentFilters.sort) queryString += `&sort=${currentFilters.sort}`;
    
    queryString += `&page=${page}&per_page=${perPage}`;
    
    const container = document.getElementById('productsContainer');
    container.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading products...</p>
        </div>
    `;
    
    fetch(`../actions/filter_products_action.php?${queryString}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayProducts(data.data || []);
                renderPagination(data.total_pages, data.page);
            } else {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading page:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>An error occurred while loading products
                </div>
            `;
        });
}

/**
 * Load package items for a product
 */
function loadProductPackageItems(productId) {
    fetch(`../actions/get_package_items_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.items && data.items.length > 0) {
                displayPackageItemsPreview(productId, data.items);
            }
        })
        .catch(error => {
            console.error('Error loading package items:', error);
        });
}

/**
 * Display package items preview on product card
 */
function displayPackageItemsPreview(productId, items) {
    const container = document.getElementById(`packageItems${productId}`);
    if (!container) return;
    
    // Show first 3 items as a preview
    const previewItems = items.slice(0, 3);
    const hasMore = items.length > 3;
    
    let html = '<div class="package-preview-section" onclick="event.stopPropagation();">';
    html += '<div class="package-preview-title"><i class="bi bi-check2-square"></i> Includes (click to customize):</div>';
    html += '<div class="package-preview-items">';
    
    previewItems.forEach(item => {
        html += `
            <div class="package-preview-item">
                <i class="bi bi-check-circle-fill"></i>
                <span>${escapeHtml(item.item_name)}</span>
            </div>
        `;
    });
    
    if (hasMore) {
        html += `<div class="package-preview-more">+${items.length - 3} more items</div>`;
    }
    
    html += '</div></div>';
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

/**
 * Toggle wishlist from products page
 */
function toggleWishlistFromProducts(productId, btn) {
    const icon = btn.querySelector('i');
    const isFilled = icon.classList.contains('bi-heart-fill');
    
    btn.disabled = true;
    
    const action = isFilled ? 'remove_from_wishlist_action.php' : 'add_to_wishlist_action.php';
    
    fetch(`../actions/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (isFilled) {
                // Remove from wishlist
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                btn.style.color = '';
            } else {
                // Add to wishlist
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                btn.style.color = '#dc2626';
            }
            
            if (typeof showSuccessMessage === 'function') {
                showSuccessMessage(data.message);
            }
            
            if (data.wishlist_count !== undefined && typeof updateWishlistCount === 'function') {
                updateWishlistCount(data.wishlist_count);
            }
        } else {
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(data.message);
            } else {
                alert(data.message);
            }
        }
        btn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof showErrorMessage === 'function') {
            showErrorMessage('Failed to update wishlist');
        }
        btn.disabled = false;
    });
}
