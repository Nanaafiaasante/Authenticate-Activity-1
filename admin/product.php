<?php
/**
 * Admin Product Management Page
 * Provides CREATE and UPDATE interface for product management
 * Only accessible by admin users
 * 
 * For VendorConnect Ghana: Manages wedding planner service profiles
 */

require_once '../settings/core.php';

// Check if user is logged in
if (!check_login()) {
    header("Location: ../login/login.php");
    exit;
}

// Check if user is admin
if (!check_admin()) {
    header("Location: ../login/login.php");
    exit;
}

$page_title = "Service Management - Planner Profiles";

// Check if we're in edit mode
$edit_mode = isset($_GET['edit']) && !empty($_GET['edit']);
$product_id = $edit_mode ? $_GET['edit'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/product.css">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="bi bi-briefcase-fill me-2"></i>
                        VendorConnect Ghana
                    </h2>
                    <small class="text-muted">Planner Service Profile Management</small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="category.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-tags me-1"></i>Categories
                    </a>
                    <a href="brand.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-bag me-1"></i>Brands
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                    <a href="../login/logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Add/Edit Product Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $edit_mode ? 'pencil-square' : 'plus-circle'; ?> me-2"></i>
                    <?php echo $edit_mode ? 'Edit Service Profile' : 'Add New Service Profile'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form id="productForm">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" id="productId" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <?php endif; ?>
                    <input type="hidden" id="productImagePath" name="product_image">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Service Category *</label>
                                <select class="form-select" id="productCategory" name="product_cat" required>
                                    <option value="">Select Category...</option>
                                </select>
                                <small class="text-muted">Select the main service category</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="productBrand" class="form-label">Brand *</label>
                                <select class="form-select" id="productBrand" name="product_brand" required>
                                    <option value="">Select Brand...</option>
                                </select>
                                <small class="text-muted">Select your brand</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="productTitle" class="form-label">Service Title *</label>
                        <input type="text" class="form-control" id="productTitle" name="product_title" 
                               placeholder="e.g., Premium Traditional Wedding Planning Package" 
                               required maxlength="200">
                        <small class="text-muted">Give your service a clear, descriptive title (max 200 characters)</small>
                    </div>

                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Service Price (GHS) *</label>
                        <input type="number" class="form-control" id="productPrice" name="product_price" 
                               placeholder="e.g., 5000" step="0.01" min="0" required>
                        <small class="text-muted">Enter the base price for this service in Ghana Cedis</small>
                    </div>

                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Service Description</label>
                        <textarea class="form-control" id="productDescription" name="product_desc" 
                                  rows="4" maxlength="500" 
                                  placeholder="Describe what's included in this service package, your experience, and what makes your offering unique..."></textarea>
                        <small class="text-muted">Describe your service (max 500 characters)</small>
                    </div>

                    <div class="mb-3">
                        <label for="productKeywords" class="form-label">Keywords (for search)</label>
                        <input type="text" class="form-control" id="productKeywords" name="product_keywords" 
                               placeholder="e.g., traditional wedding, Akan ceremony, cultural events" 
                               maxlength="100">
                        <small class="text-muted">Add keywords to help couples find your services (comma-separated)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service Image (Portfolio)</label>
                        <div class="image-upload-area" id="imageUploadArea">
                            <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                            <p class="mb-2">Click to upload or drag and drop</p>
                            <p class="text-muted small">PNG, JPG, GIF or WebP (Max 5MB)</p>
                            <input type="file" id="productImageInput" accept="image/*" style="display: none;">
                        </div>
                        <div class="image-preview-container">
                            <img id="imagePreview" class="preview-image" style="display: none;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="product.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="submit" class="btn btn-<?php echo $edit_mode ? 'warning' : 'primary'; ?>">
                            <i class="bi bi-<?php echo $edit_mode ? 'check' : 'plus'; ?> me-1"></i>
                            <?php echo $edit_mode ? 'Update Service' : 'Add Service'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Existing Products List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Your Service Profiles
                </h5>
                <button class="btn btn-outline-primary btn-sm" onclick="loadProducts()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="productsContainer">
                    <div class="text-center">
                        <div class="loading show">
                            <i class="bi bi-hourglass-split me-2"></i>Loading services...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this service profile?</p>
                    <p class="text-muted small">This action cannot be undone. All associated data will be permanently removed.</p>
                    <input type="hidden" id="deleteProductId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>Delete Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product.js"></script>
    <script>
        // Pass edit mode and product ID to JavaScript
        const EDIT_MODE = <?php echo $edit_mode ? 'true' : 'false'; ?>;
        const PRODUCT_ID = <?php echo $edit_mode ? $product_id : 'null'; ?>;

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadBrands();
            loadProducts();
            
            // If in edit mode, load product data
            if (EDIT_MODE && PRODUCT_ID) {
                setTimeout(() => {
                    loadProductForEdit(PRODUCT_ID);
                }, 500);
            }
        });
    </script>
</body>
</html>
