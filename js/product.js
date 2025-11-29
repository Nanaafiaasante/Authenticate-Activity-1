/**
 * Product Management JavaScript
 * Handles CRUD operations for products with validation, AJAX calls, and image upload
 * For VendorConnect Ghana: Manages wedding planner service profiles
 */

// Global variables
let deleteProductModal;
let products = [];
let categories = [];
let brands = [];
let currentImagePath = '';

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    deleteProductModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
    
    // Add event listener for product form
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (typeof EDIT_MODE !== 'undefined' && EDIT_MODE) {
            updateProduct();
        } else {
            addProduct();
        }
    });

    // Package items functionality
    initializePackageItems();

    // Image upload handling
    const uploadArea = document.getElementById('imageUploadArea');
    const imageInput = document.getElementById('productImageInput');
    const imagePreview = document.getElementById('imagePreview');

    uploadArea.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            handleImageUpload(e.target.files[0]);
        }
    });

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = 'rgba(124, 155, 181, 0.2)';
    });

    uploadArea.addEventListener('dragleave', (e) => {
        uploadArea.style.backgroundColor = '';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '';
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleImageUpload(e.dataTransfer.files[0]);
        }
    });

    // Category change handler removed - brands are independent of categories
});

/**
 * Display alert message
 */
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.appendChild(alert);
    
    setTimeout(() => alert.remove(), 5000);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Validate product data
 */
function validateProductData(data) {
    if (!data.product_cat || data.product_cat === '') {
        showAlert('Please select a service category', 'danger');
        return false;
    }

    if (!data.product_brand || data.product_brand === '') {
        showAlert('Please select a brand', 'danger');
        return false;
    }

    if (!data.product_title || data.product_title.trim() === '') {
        showAlert('Service title is required', 'danger');
        return false;
    }

    if (data.product_title.length > 200) {
        showAlert('Service title must be less than 200 characters', 'danger');
        return false;
    }

    if (!data.product_price || data.product_price === '') {
        showAlert('Service price is required', 'danger');
        return false;
    }

    if (isNaN(data.product_price) || parseFloat(data.product_price) < 0) {
        showAlert('Valid price is required (must be a positive number)', 'danger');
        return false;
    }

    if (data.product_desc && data.product_desc.length > 500) {
        showAlert('Description must be less than 500 characters', 'danger');
        return false;
    }

    return true;
}

/**
 * Handle image upload
 */
function handleImageUpload(file) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed', 'danger');
        return;
    }

    // Validate file size (5MB)
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        showAlert('File size exceeds maximum limit of 5MB', 'danger');
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.src = e.target.result;
        imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(file);

    // Upload image
    const formData = new FormData();
    formData.append('product_image', file);
    
    // Add product ID if in edit mode
    if (typeof EDIT_MODE !== 'undefined' && EDIT_MODE && typeof PRODUCT_ID !== 'undefined') {
        formData.append('product_id', PRODUCT_ID);
    }

    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            currentImagePath = data.file_path;
            document.getElementById('productImagePath').value = data.file_path;
            showAlert('<i class="bi bi-check-circle me-2"></i>Image uploaded successfully', 'success');
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while uploading image', 'danger');
    });
}

/**
 * Load categories for dropdown
 */
function loadCategories() {
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                categories = data.data;
                populateCategoryDropdown();
            } else {
                showAlert('Failed to load categories: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while loading categories', 'danger');
        });
}

/**
 * Populate category dropdown
 */
function populateCategoryDropdown() {
    const dropdown = document.getElementById('productCategory');
    dropdown.innerHTML = '<option value="">Select Category...</option>';
    
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.cat_id;
        option.textContent = category.cat_name;
        dropdown.appendChild(option);
    });
}

/**
 * Load brands for dropdown
 */
function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                brands = data.data;
                populateBrandDropdown();
            } else {
                showAlert('Failed to load brands: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while loading brands', 'danger');
        });
}

/**
 * Populate brand dropdown
 */
function populateBrandDropdown() {
    const dropdown = document.getElementById('productBrand');
    dropdown.innerHTML = '<option value="">Select Brand...</option>';
    
    brands.forEach(brand => {
        const option = document.createElement('option');
        option.value = brand.brand_id;
        option.textContent = brand.brand_name;
        dropdown.appendChild(option);
    });
}

/**
 * Filter brands by selected category - REMOVED
 * Brands are now independent of categories
 */

/**
 * Load all products
 */
function loadProducts() {
    const container = document.getElementById('productsContainer');
    
    // Only load if container exists (on dashboard, not on product.php)
    if (!container) {
        return;
    }
    
    container.innerHTML = '<div class="text-center"><div class="loading show"><i class="bi bi-hourglass-split me-2"></i>Loading services...</div></div>';
    
    fetch('../actions/fetch_product_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                products = data.data;
                displayProducts();
            } else {
                container.innerHTML = '<div class="alert alert-warning">' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="alert alert-danger">An error occurred while loading services</div>';
        });
}

/**
 * Display products in cards
 */
function displayProducts() {
    const container = document.getElementById('productsContainer');
    
    if (products.length === 0) {
        container.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No services found. Add your first service above.</div>';
        return;
    }
    
    let html = '<div class="row">';
    products.forEach(product => {
        const imagePath = product.product_image || '../uploads/default-product.jpg';
        html += `
            <div class="col-md-4 mb-4">
                <div class="card product-card h-100">
                    <img src="../${imagePath}" class="card-img-top product-image-preview" alt="${product.product_title}" 
                         onerror="this.src='../uploads/default-product.jpg'">
                    <div class="card-body">
                        <h6 class="card-title">${product.product_title}</h6>
                        <p class="card-text text-muted small">
                            <span class="badge bg-secondary">${product.cat_name}</span>
                            <span class="badge bg-info">${product.brand_name}</span>
                        </p>
                        <p class="card-text"><strong>GHS ${parseFloat(product.product_price).toFixed(2)}</strong></p>
                        <p class="card-text small">${product.product_desc ? product.product_desc.substring(0, 100) + '...' : 'No description'}</p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <button class="btn btn-warning btn-sm me-1" onclick="window.location.href='product.php?edit=${product.product_id}'">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${product.product_id})">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

/**
 * Add new product
 */
function addProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    
    const productData = {
        product_cat: formData.get('product_cat'),
        product_brand: formData.get('product_brand'),
        product_title: formData.get('product_title'),
        product_price: formData.get('product_price'),
        product_desc: formData.get('product_desc'),
        product_keywords: formData.get('product_keywords'),
        product_image: formData.get('product_image')
    };
    
    if (!validateProductData(productData)) return;
    
    fetch('../actions/add_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Save package items if product was created successfully
            const productId = data.product_id;
            return savePackageItems(productId).then(packageResult => {
                showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
                form.reset();
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('packageItemsContainer').innerHTML = '';
                packageItemsCount = 0;
                currentImagePath = '';
                loadProducts();
            });
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding the service', 'danger');
    });
}

/**
 * Load product for editing
 */
function loadProductForEdit(productId) {
    const product = products.find(p => p.product_id == productId);
    
    if (!product) {
        showAlert('Service not found', 'danger');
        return;
    }
    
    document.getElementById('productCategory').value = product.product_cat;
    filterBrandsByCategory(product.product_cat);
    
    setTimeout(() => {
        document.getElementById('productBrand').value = product.product_brand;
    }, 100);
    
    document.getElementById('productTitle').value = product.product_title;
    document.getElementById('productPrice').value = product.product_price;
    document.getElementById('productDescription').value = product.product_desc || '';
    document.getElementById('productKeywords').value = product.product_keywords || '';
    
    // Load package items for this product
    loadPackageItems(productId);
    
    if (product.product_image) {
        document.getElementById('productImagePath').value = product.product_image;
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.src = '../' + product.product_image;
        imagePreview.style.display = 'block';
        currentImagePath = product.product_image;
    }
}

/**
 * Update product
 */
function updateProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    
    const productData = {
        product_id: formData.get('product_id'),
        product_cat: formData.get('product_cat'),
        product_brand: formData.get('product_brand'),
        product_title: formData.get('product_title'),
        product_price: formData.get('product_price'),
        product_desc: formData.get('product_desc'),
        product_keywords: formData.get('product_keywords'),
        product_image: formData.get('product_image')
    };
    
    if (!validateProductData(productData)) return;
    
    fetch('../actions/update_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Save package items after updating product
            const productId = productData.product_id;
            return savePackageItems(productId).then(packageResult => {
                showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
                loadProducts();
            });
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating the service', 'danger');
    });
}

/**
 * Open delete confirmation modal
 */
function openDeleteModal(productId) {
    document.getElementById('deleteProductId').value = productId;
    deleteProductModal.show();
}

/**
 * Confirm and delete product
 */
function confirmDelete() {
    const productId = document.getElementById('deleteProductId').value;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('../actions/delete_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
            deleteProductModal.hide();
            loadProducts();
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the service', 'danger');
    });
}

/**
 * ===================================================================
 * PACKAGE ITEMS FUNCTIONALITY
 * ===================================================================
 */

let packageItemsCount = 0;

/**
 * Initialize package items functionality
 */
function initializePackageItems() {
    const addBtn = document.getElementById('addPackageItemBtn');
    if (addBtn) {
        addBtn.addEventListener('click', addPackageItemField);
    }
    
    // If editing, load existing package items
    if (typeof EDIT_MODE !== 'undefined' && EDIT_MODE && typeof PRODUCT_ID !== 'undefined') {
        loadPackageItems(PRODUCT_ID);
    }
}

/**
 * Add a new package item field
 */
function addPackageItemField(itemData = null) {
    const container = document.getElementById('packageItemsContainer');
    const itemId = itemData ? itemData.item_id : `new_${packageItemsCount++}`;
    const itemName = itemData ? itemData.item_name : '';
    const isOptional = itemData ? itemData.is_optional : 1;
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'package-item-field mb-3';
    itemDiv.dataset.itemId = itemId;
    itemDiv.innerHTML = `
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-11">
                        <label class="form-label small">Item Name *</label>
                        <input type="text" class="form-control form-control-sm package-item-name" 
                               placeholder="e.g., Food, Transportation, Venue, Decorations" 
                               value="${itemName}" required>
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input package-item-optional" 
                                   ${isOptional ? 'checked' : ''}>
                            <label class="form-check-label small text-muted">
                                Customer can deselect this item (optional)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-start">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removePackageItem(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(itemDiv);
}

/**
 * Remove a package item field
 */
function removePackageItem(button) {
    const itemDiv = button.closest('.package-item-field');
    itemDiv.remove();
}

/**
 * Get all package items from the form
 */
function getPackageItemsData() {
    const items = [];
    const itemFields = document.querySelectorAll('.package-item-field');
    
    itemFields.forEach(field => {
        const name = field.querySelector('.package-item-name').value.trim();
        const isOptional = field.querySelector('.package-item-optional').checked ? 1 : 0;
        const itemId = field.dataset.itemId;
        
        if (name) {
            items.push({
                item_id: itemId.startsWith('new_') ? null : itemId,
                item_name: name,
                is_optional: isOptional
            });
        }
    });
    
    return items;
}

/**
 * Load package items for a product (edit mode)
 */
function loadPackageItems(productId) {
    fetch(`../actions/get_package_items_action.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    addPackageItemField(item);
                });
            }
        })
        .catch(error => {
            console.error('Error loading package items:', error);
        });
}

/**
 * Save package items for a product
 */
function savePackageItems(productId) {
    const items = getPackageItemsData();
    
    if (items.length === 0) {
        return Promise.resolve({ status: 'success' });
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('items', JSON.stringify(items));
    
    return fetch('../actions/save_package_items_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json());
}
