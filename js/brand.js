/**
 * Brand Management JavaScript
 * Handles CRUD operations for brands with validation and AJAX calls
 * For VendorConnect Ghana: Manages brands
 */

// Global variables
let editBrandModal;
let deleteBrandModal;
let brands = [];

/**
 * Escape HTML to prevent XSS attacks
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize modals when page loads
document.addEventListener('DOMContentLoaded', function() {
    editBrandModal = new bootstrap.Modal(document.getElementById('editBrandModal'));
    deleteBrandModal = new bootstrap.Modal(document.getElementById('deleteBrandModal'));
    
    // Add event listener for add brand form
    document.getElementById('addBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addBrand();
    });
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
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
    
    // Scroll to top to show alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/**
 * Validate brand name
 */
function validateBrandName(brandName) {
    if (!brandName || brandName.trim() === '') {
        showAlert('Brand name is required', 'danger');
        return false;
    }
    
    if (brandName.length > 100) {
        showAlert('Brand name must be less than 100 characters', 'danger');
        return false;
    }
    
    // Check for valid characters (alphanumeric, spaces, hyphens, and common punctuation)
    const validPattern = /^[a-zA-Z0-9\s\-,.'&]+$/;
    if (!validPattern.test(brandName)) {
        showAlert('Brand name contains invalid characters', 'danger');
        return false;
    }
    
    return true;
}



/**
 * Load all brands
 */
function loadBrands() {
    const container = document.getElementById('brandsContainer');
    container.innerHTML = '<div class="text-center"><div class="loading show"><i class="bi bi-hourglass-split me-2"></i>Loading brands...</div></div>';
    
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                brands = data.data;
                displayBrands();
            } else {
                container.innerHTML = '<div class="alert alert-warning">Failed to load brands: ' + data.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="alert alert-danger">An error occurred while loading brands</div>';
        });
}

/**
 * Display brands
 */
function displayBrands() {
    const container = document.getElementById('brandsContainer');
    
    if (brands.length === 0) {
        container.innerHTML = '<div class="loading-state"><i class="bi bi-inbox me-2"></i>No brands found. Add your first brand above.</div>';
        return;
    }
    
    // Generate HTML
    let html = `
        <div class="table-responsive">
            <table class="management-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Brand Name</th>
                        <th style="width: 200px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    brands.forEach(brand => {
        html += `
            <tr>
                <td><strong>#${brand.brand_id}</strong></td>
                <td>${escapeHtml(brand.brand_name)}</td>
                <td style="text-align: center;">
                    <button class="table-action-btn btn-edit" onclick="editBrand(${brand.brand_id})">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="table-action-btn btn-delete" onclick="deleteBrand(${brand.brand_id})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

/**
 * Add new brand
 */
function addBrand() {
    const form = document.getElementById('addBrandForm');
    const formData = new FormData(form);
    
    const brandName = formData.get('brand_name');
    
    // Validate inputs
    if (!validateBrandName(brandName)) return;
    
    // Make AJAX request
    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
            form.reset();
            loadBrands();
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    showAlert('An error occurred while adding the brand', 'danger');
    });
}

/**
 * Open edit modal
 */
function openEditModal(brandId) {
    const brand = brands.find(b => b.brand_id == brandId);
    
    if (!brand) {
        showAlert('Brand not found', 'danger');
        return;
    }
    
    document.getElementById('editBrandId').value = brand.brand_id;
    document.getElementById('editBrandName').value = brand.brand_name;
    
    editBrandModal.show();
}

/**
 * Update brand
 */
function updateBrand() {
    const brandId = document.getElementById('editBrandId').value;
    const brandName = document.getElementById('editBrandName').value;
    
    // Validate inputs
    if (!validateBrandName(brandName)) return;
    
    const formData = new FormData();
    formData.append('brand_id', brandId);
    formData.append('brand_name', brandName);
    
    // Make AJAX request
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
            editBrandModal.hide();
            loadBrands();
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    showAlert('An error occurred while updating the brand', 'danger');
    });
}

/**
 * Open delete confirmation modal
 */
function openDeleteModal(brandId) {
    document.getElementById('deleteBrandId').value = brandId;
    deleteBrandModal.show();
}

/**
 * Confirm and delete brand
 */
function confirmDelete() {
    const brandId = document.getElementById('deleteBrandId').value;
    
    const formData = new FormData();
    formData.append('brand_id', brandId);
    
    // Make AJAX request
    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert('<i class="bi bi-check-circle me-2"></i>' + data.message, 'success');
            deleteBrandModal.hide();
            loadBrands();
        } else {
            showAlert('<i class="bi bi-exclamation-triangle me-2"></i>' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    showAlert('An error occurred while deleting the brand', 'danger');
    });
}
