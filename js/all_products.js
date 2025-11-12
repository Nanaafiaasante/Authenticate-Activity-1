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
    
    let html = '<div class="row g-4">';
    
    products.forEach(product => {
        const imageUrl = product.product_image ? `../${product.product_image}` : '../uploads/default-product.jpg';
        const price = parseFloat(product.product_price).toFixed(2);
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="product-card">
                    <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" class="product-image" 
                         onerror="this.src='../uploads/default-product.jpg'">
                    <div class="product-body">
                        <h5 class="product-title">${escapeHtml(product.product_title)}</h5>
                        <p class="product-vendor">
                            <i class="bi bi-person"></i> by ${escapeHtml(product.vendor_name)}
                        </p>
                        <div class="product-price">GHS ${price}</div>
                        <div class="product-badges">
                            <span class="badge-custom">${escapeHtml(product.cat_name)}</span>
                            <span class="badge-custom badge-brand">${escapeHtml(product.brand_name)}</span>
                        </div>
                        <div class="product-actions">
                            <a href="single_product.php?id=${product.product_id}" class="btn-view-details">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                            <button class="btn-add-cart add-to-cart-btn" data-product-id="${product.product_id}" data-quantity="1">
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
    const filters = {
        category: document.getElementById('categoryFilter').value,
        brand: document.getElementById('brandFilter').value,
        min_price: document.getElementById('minPrice').value,
        max_price: document.getElementById('maxPrice').value,
        search: document.getElementById('searchInput').value,
        sort: document.getElementById('sortFilter').value
    };
    
    currentFilters = filters;
    
    // Build query string
    let queryString = 'type=composite';
    
    if (filters.category) queryString += `&category=${filters.category}`;
    if (filters.brand) queryString += `&brand=${filters.brand}`;
    if (filters.min_price) queryString += `&min_price=${filters.min_price}`;
    if (filters.max_price) queryString += `&max_price=${filters.max_price}`;
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
    
    // Previous button
    if (currentPageNum > 1) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadProducts(${currentPageNum - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                 </li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPageNum - 2 && i <= currentPageNum + 2)) {
            const activeClass = i === currentPageNum ? 'active' : '';
            html += `<li class="page-item ${activeClass}">
                        <a class="page-link" href="#" onclick="loadProducts(${i}); return false;">${i}</a>
                     </li>`;
        } else if (i === currentPageNum - 3 || i === currentPageNum + 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    // Next button
    if (currentPageNum < totalPages) {
        html += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadProducts(${currentPageNum + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                 </li>`;
    }
    
    html += '</ul>';
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
