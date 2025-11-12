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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/brand.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="header-container">
                <!-- Left: Logo -->
                <div class="header-left">
                    <div class="vc-logo">
                        <div class="logo-ring ring-outer"></div>
                        <div class="logo-ring ring-middle"></div>
                        <div class="logo-ring ring-inner"></div>
                        <span class="logo-text">VC</span>
                    </div>
                    <span class="vc-brand">VendorConnect Ghana</span>
                </div>
                
                <!-- Center: Page Title -->
                <div class="header-center">
                    <h1 class="page-title">
                        <i class="bi bi-tags me-2"></i>Brand Management
                    </h1>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="dashboard.php" class="header-nav-btn">
                        <i class="bi bi-speedometer2"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="category.php" class="header-nav-btn">
                        <i class="bi bi-folder2-open"></i>
                        <span class="nav-label">Categories</span>
                    </a>
                    <a href="../view/all_products.php" class="header-nav-btn">
                        <i class="bi bi-grid"></i>
                        <span class="nav-label">Store</span>
                    </a>
                    <a href="../login/logout.php" class="header-nav-btn logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid" style="max-width: 1200px;">
            <!-- Alert Messages -->
            <div id="alertContainer"></div>

            <!-- Add Brand Card -->
            <div class="management-card mb-4">
                <div class="management-card-header">
                    <h3 class="management-card-title">
                        <i class="bi bi-plus-circle me-2"></i>Add New Brand
                    </h3>
                </div>
                <div class="management-card-body">
                    <form id="addBrandForm">
                        <div class="row align-items-end">
                            <div class="col-md-9">
                                <label for="brandName" class="form-label">Brand Name</label>
                                <input type="text" class="form-control form-control-lg" id="brandName" name="brand_name" 
                                       placeholder="e.g., Elite Events, Traditional Weddings, Beach Ceremonies" required maxlength="100">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn-action btn-primary w-100">
                                    <i class="bi bi-plus me-1"></i>Add Brand
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Brands List Card -->
            <div class="management-card">
                <div class="management-card-header">
                    <h3 class="management-card-title">
                        <i class="bi bi-list-ul me-2"></i>All Brands
                    </h3>
                    <button class="btn-action btn-secondary" onclick="loadBrands()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
                <div class="management-card-body">
                    <div id="brandsContainer">
                        <div class="text-center">
                            <div class="loading-state">
                                <i class="bi bi-hourglass-split me-2"></i>Loading brands...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
