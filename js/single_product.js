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
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="product-image-section">
                        <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" 
                             class="product-main-image" onerror="this.src='../uploads/default-product.jpg'">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-info-section">
                        <h1 class="product-title-large">${escapeHtml(product.product_title)}</h1>
                        
                        <div class="product-price-large">GHS ${price}</div>
                        
                        <div class="mb-3">
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
                                <div class="meta-label mb-2">Keywords</div>
                                ${keywords.map(kw => `<span class="keyword-tag">${escapeHtml(kw.trim())}</span>`).join('')}
                            </div>
                        ` : ''}
                        
                        <button class="btn btn-cart" onclick="addToCart(${product.product_id})">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="vendor-card">
            <div class="vendor-title">
                <i class="bi bi-person-circle me-2"></i>Vendor Information
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Vendor Name</div>
                            <div class="meta-value">${escapeHtml(product.vendor_name)}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Email</div>
                            <div class="meta-value">${escapeHtml(product.vendor_email)}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Contact</div>
                            <div class="meta-value">${escapeHtml(product.vendor_contact)}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="meta-item">
                        <div class="meta-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="meta-content">
                            <div class="meta-label">Location</div>
                            <div class="meta-value">${escapeHtml(product.vendor_city)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

/**
 * Add to cart (placeholder)
 */
function addToCart(productId) {
    alert('Added to cart (frontend only)');
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
