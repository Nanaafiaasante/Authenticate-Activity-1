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
    
    let html = '<div class="row">';
    products.forEach(product => {
        const imageUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
        const price = parseFloat(product.product_price).toFixed(2);
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="product-card">
                    <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" 
                         class="product-image" onerror="this.src='../uploads/default-product.jpg'">
                    <div class="product-body">
                        <h5 class="product-title">${escapeHtml(product.product_title)}</h5>
                        <p class="product-vendor">
                            <i class="bi bi-person me-1"></i>${escapeHtml(product.vendor_name || 'Unknown Vendor')}
                        </p>
                        <div class="product-badges">
                            <span class="badge-custom">${escapeHtml(product.cat_name || 'Uncategorized')}</span>
                            <span class="badge-custom badge-brand">${escapeHtml(product.brand_name || 'No Brand')}</span>
                        </div>
                        <div class="product-price">GHS ${price}</div>
                        <div class="product-actions">
                            <a href="single_product.php?id=${product.product_id}" class="btn btn-primary-custom">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                            <button class="btn-add-cart" onclick="addToCart(${product.product_id}, '${escapeHtml(product.product_title).replace(/'/g, "\\'")}')">
                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
    renderPagination(totalPages, currentPage);
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
 * Render pagination
 */
function renderPagination(totalPages, currentPage) {
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
