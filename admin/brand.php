<?php
/**
 * Admin Brand Management Page
 * Provides CRUD interface for brand management
 * Only accessible by admin users
 * 
 * For VendorConnect Ghana: Manages brands
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

$page_title = "Brand Management";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/brand.css">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="bi bi-bag-fill me-2"></i>
                        VendorConnect Ghana
                    </h2>
                    <small class="text-muted">Brand Management</small>
                </div>
                <div class="col-md-6 text-end">
                    <a href="category.php" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-tags me-1"></i>Categories
                    </a>
                    <a href="../index.php" class="btn btn-outline-secondary me-2">
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

        <!-- Add Brand Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New Brand
                </h5>
            </div>
            <div class="card-body">
                <form id="addBrandForm">
                    <div class="row">
                        <div class="col-md-9">
                            <label for="brandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brandName" name="brand_name" 
                                   placeholder="e.g., Traditional Weddings, Beach Weddings, Cultural Ceremonies" required maxlength="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus me-1"></i>Add Brand
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Brands List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Brands
                </h5>
                <button class="btn btn-outline-primary btn-sm" onclick="loadBrands()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <div id="brandsContainer">
                    <div class="text-center">
                        <div class="loading show">
                            <i class="bi bi-hourglass-split me-2"></i>Loading brands...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Brand
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBrandForm">
                        <input type="hidden" id="editBrandId" name="brand_id">
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="editBrandName" name="brand_name" 
                                   required maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateBrand()">
                        <i class="bi bi-check me-1"></i>Update Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteBrandModal" tabindex="-1">
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
                    <p>Are you sure you want to delete this brand?</p>
                    <p class="text-muted small">This action cannot be undone. Make sure no products are using this brand.</p>
                    <input type="hidden" id="deleteBrandId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>Delete Brand
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/brand.js"></script>
    <script>
        // Load brands when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadBrands();
        });
    </script>
</body>
</html>
