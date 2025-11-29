/**
 * Category Management JavaScript
 * Handles frontend validation and AJAX calls for category CRUD operations
 */

// Global variables
let categories = [];

/**
 * Show alert message
 * @param {string} message - Alert message
 * @param {string} type - Alert type (success, error, warning, info)
 */
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHtml;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 5000);
}

/**
 * Get appropriate icon for alert type
 * @param {string} type - Alert type
 * @returns {string} Icon class
 */
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Validate category name
 * @param {string} name - Category name
 * @returns {object} Validation result
 */
function validateCategoryName(name) {
    const trimmedName = name.trim();
    
    if (!trimmedName) {
        return {
            valid: false,
            message: 'Category name is required'
        };
    }
    
    if (trimmedName.length < 2) {
        return {
            valid: false,
            message: 'Category name must be at least 2 characters long'
        };
    }
    
    if (trimmedName.length > 100) {
        return {
            valid: false,
            message: 'Category name must be less than 100 characters'
        };
    }
    
    // Check for duplicate names
    const existingCategory = categories.find(cat => 
        cat.cat_name.toLowerCase() === trimmedName.toLowerCase()
    );
    
    if (existingCategory) {
        return {
            valid: false,
            message: 'Category name already exists'
        };
    }
    
    return {
        valid: true,
        message: 'Valid category name'
    };
}

/**
 * Load all categories from server
 */
function loadCategories() {
    const tableBody = document.getElementById('categoriesTableBody');
    const loadingElement = tableBody.querySelector('.loading');
    
    if (loadingElement) {
        loadingElement.classList.add('show');
    }
    
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                categories = data.data;
                displayCategories(categories);
                showAlert(`Loaded ${data.count} categories successfully`, 'success');
            } else {
                showAlert(data.message || 'Failed to load categories', 'error');
                displayEmptyState();
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            showAlert('Network error while loading categories', 'error');
            displayEmptyState();
        })
        .finally(() => {
            if (loadingElement) {
                loadingElement.classList.remove('show');
            }
        });
}

/**
 * Display categories in table
 * @param {Array} categoriesList - Array of categories
 */
function displayCategories(categoriesList) {
    const tableBody = document.getElementById('categoriesTableBody');
    
    if (!categoriesList || categoriesList.length === 0) {
        displayEmptyState();
        return;
    }
    
    let html = '';
    categoriesList.forEach(category => {
        html += `
            <tr>
                <td><strong>#${category.cat_id}</strong></td>
                <td>${escapeHtml(category.cat_name)}</td>
                <td style="text-align: center;">
                    <button class="table-action-btn btn-edit" onclick="editCategory(${category.cat_id})">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="table-action-btn btn-delete" onclick="deleteCategory(${category.cat_id})">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

/**
 * Display empty state when no categories
 */
function displayEmptyState() {
    const tableBody = document.getElementById('categoriesTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="3">
                <div class="loading-state">
                    <i class="bi bi-inbox me-2"></i>
                    No categories found. Add your first category above.
                </div>
            </td>
        </tr>
    `;
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Add new category
 */
function addCategory() {
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);
    const categoryName = formData.get('cat_name');
    
    // Validate input
    const validation = validateCategoryName(categoryName);
    if (!validation.valid) {
        showAlert(validation.message, 'error');
        return;
    }
    
    // Show loading state
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adding...';
    submitButton.disabled = true;
    
    fetch('../actions/add_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            form.reset();
            loadCategories(); // Reload the list
        } else {
            showAlert(data.message || 'Failed to add category', 'error');
        }
    })
    .catch(error => {
        console.error('Error adding category:', error);
        showAlert('Network error while adding category', 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

/**
 * Edit category - open modal
 * @param {number} categoryId - Category ID
 */
function editCategory(categoryId) {
    const category = categories.find(cat => cat.cat_id == categoryId);
    if (!category) {
        showAlert('Category not found', 'error');
        return;
    }
    
    document.getElementById('editCategoryId').value = category.cat_id;
    document.getElementById('editCategoryName').value = category.cat_name;
    
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

/**
 * Update category
 */
function updateCategory() {
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);
    const categoryId = formData.get('cat_id');
    const categoryName = formData.get('cat_name');
    
    // Validate input
    const validation = validateCategoryName(categoryName);
    if (!validation.valid) {
        showAlert(validation.message, 'error');
        return;
    }
    
    // Show loading state
    const updateButton = document.querySelector('#editCategoryModal .btn-primary');
    const originalText = updateButton.innerHTML;
    updateButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Updating...';
    updateButton.disabled = true;
    
    fetch('../actions/update_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
            modal.hide();
            loadCategories(); // Reload the list
        } else {
            showAlert(data.message || 'Failed to update category', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating category:', error);
        showAlert('Network error while updating category', 'error');
    })
    .finally(() => {
        updateButton.innerHTML = originalText;
        updateButton.disabled = false;
    });
}

/**
 * Delete category - open confirmation modal
 * @param {number} categoryId - Category ID
 */
function deleteCategory(categoryId) {
    const category = categories.find(cat => cat.cat_id == categoryId);
    if (!category) {
        showAlert('Category not found', 'error');
        return;
    }
    
    document.getElementById('deleteCategoryId').value = categoryId;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    modal.show();
}

/**
 * Confirm delete category
 */
function confirmDelete() {
    const categoryId = document.getElementById('deleteCategoryId').value;
    
    if (!categoryId) {
        showAlert('Category ID not found', 'error');
        return;
    }
    
    // Show loading state
    const deleteButton = document.querySelector('#deleteCategoryModal .btn-danger');
    const originalText = deleteButton.innerHTML;
    deleteButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
    deleteButton.disabled = true;
    
    const formData = new FormData();
    formData.append('cat_id', categoryId);
    
    fetch('../actions/delete_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showAlert(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
            modal.hide();
            loadCategories(); // Reload the list
        } else {
            showAlert(data.message || 'Failed to delete category', 'error');
        }
    })
    .catch(error => {
        console.error('Error deleting category:', error);
        showAlert('Network error while deleting category', 'error');
    })
    .finally(() => {
        deleteButton.innerHTML = originalText;
        deleteButton.disabled = false;
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add category form submission
    const addForm = document.getElementById('addCategoryForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addCategory();
        });
    }
    
    // Real-time validation for add form
    const categoryNameInput = document.getElementById('categoryName');
    if (categoryNameInput) {
        categoryNameInput.addEventListener('input', function() {
            const validation = validateCategoryName(this.value);
            if (!validation.valid && this.value.trim()) {
                this.setCustomValidity(validation.message);
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Real-time validation for edit form
    const editCategoryNameInput = document.getElementById('editCategoryName');
    if (editCategoryNameInput) {
        editCategoryNameInput.addEventListener('input', function() {
            const validation = validateCategoryName(this.value);
            if (!validation.valid && this.value.trim()) {
                this.setCustomValidity(validation.message);
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
