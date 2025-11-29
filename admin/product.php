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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/product.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="header-container">
                <!-- Left: Logo -->
                <div class="header-left">
                    <a href="../index.php" class="vc-logo">
                        <div class="vc-logo-ring"></div>
                        <div class="vc-logo-text">
                            <div class="vc-logo-main">VendorConnect</div>
                            <div class="vc-logo-sub">GHANA</div>
                        </div>
                    </a>
                </div>
                
                <!-- Center: Page Title -->
                <div class="header-center">
                    <h1 class="page-title"><?php echo $edit_mode ? 'Edit Service Profile' : 'Add Service Profile'; ?></h1>
                    <p class="page-subtitle">Manage your wedding service offerings</p>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="category.php" class="header-nav-btn">
                        <i class="bi bi-tags"></i>
                        <span class="nav-label">Categories</span>
                    </a>
                    <a href="brand.php" class="header-nav-btn">
                        <i class="bi bi-bag"></i>
                        <span class="nav-label">Brands</span>
                    </a>
                    <a href="dashboard.php" class="header-nav-btn">
                        <i class="bi bi-grid"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="../login/logout.php" class="header-nav-btn logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Subscription Payment Banner -->
    <?php if (isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'pending'): ?>
    <div class="subscription-banner" style="background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%); border-left: 4px solid #f59e0b; padding: 20px; margin: 20px auto; max-width: 1400px; border-radius: 12px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 50px; height: 50px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 24px; color: #f59e0b;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1" style="color: #92400e; font-weight: 600;">
                            <i class="bi bi-lock-fill me-2"></i>Subscription Payment Required
                        </h5>
                        <p class="mb-0" style="color: #78350f; font-size: 0.95rem;">
                            Complete your payment to unlock all features and start adding products. 
                            Selected Plan: <strong><?php echo ucfirst($_SESSION['subscription_tier'] ?? 'N/A'); ?></strong>
                            (GHS <?php echo $_SESSION['subscription_tier'] === 'premium' ? '199' : '99'; ?>/month)
                        </p>
                    </div>
                </div>
                <div>
                    <a href="subscription_payment.php?tier=<?php echo $_SESSION['subscription_tier']; ?>&amount=<?php echo $_SESSION['subscription_tier'] === 'premium' ? '199' : '99'; ?>" 
                       class="btn" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="bi bi-credit-card"></i>
                        Complete Payment Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <?php if (isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'pending'): ?>
        <!-- Blocked Overlay -->
        <div class="payment-required-overlay" style="background: rgba(255, 255, 255, 0.95); position: absolute; top: 100px; left: 0; right: 0; bottom: 0; z-index: 1000; display: flex; align-items: center; justify-content: center;">
            <div class="text-center p-5">
                <i class="bi bi-lock" style="font-size: 80px; color: #f59e0b; margin-bottom: 20px;"></i>
                <h3 style="color: #92400e; margin-bottom: 15px;">Payment Required</h3>
                <p style="color: #78350f; font-size: 1.1rem; margin-bottom: 25px;">
                    Please complete your subscription payment to add products
                </p>
                <a href="subscription_payment.php?tier=<?php echo $_SESSION['subscription_tier']; ?>&amount=<?php echo $_SESSION['subscription_tier'] === 'premium' ? '199' : '99'; ?>" 
                   class="btn btn-warning btn-lg">
                    <i class="bi bi-credit-card me-2"></i>Pay Now
                </a>
            </div>
        </div>
        <?php endif; ?>

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
                                  rows="6" maxlength="1000" 
                                  placeholder="Describe what's included in this service package, your experience, and what makes your offering unique..."></textarea>
                        <small class="text-muted">Describe your service (max 1000 characters)</small>
                    </div>

                    <!-- Package Items Section -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="bi bi-check2-square me-2"></i>Package Includes
                        </label>
                        <p class="text-muted small mb-3">Add items that come with your package (e.g., Food, Transportation, Venue, Decorations).</p>
                        
                        <div id="packageItemsContainer">
                            <!-- Package items will be added here dynamically -->
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addPackageItemBtn">
                            <i class="bi bi-plus-circle me-2"></i>Add Package Item
                        </button>
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
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-grid me-1"></i>My Products
                        </a>
                        <button type="submit" class="btn btn-<?php echo $edit_mode ? 'warning' : 'primary'; ?>">
                            <i class="bi bi-<?php echo $edit_mode ? 'check' : 'plus'; ?> me-1"></i>
                            <?php echo $edit_mode ? 'Update Service' : 'Add Service'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>

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

            // Enable Enter key to submit product form
            document.getElementById('productForm').addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    this.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>
