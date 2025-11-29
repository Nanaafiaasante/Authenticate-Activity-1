<?php
session_start();

// Require admin
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header('Location: ../login/login.php');
    exit;
}

$customer_name = $_SESSION['customer_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
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
                    <h1 class="page-title">Admin Dashboard</h1>
                    <p class="page-subtitle">Welcome, <?php echo htmlspecialchars($customer_name); ?></p>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="../view/vendor_profile.php" class="header-nav-btn">
                        <i class="bi bi-person-circle"></i>
                        <span class="nav-label">My Profile</span>
                    </a>
                    <a href="consultations.php" class="header-nav-btn">
                        <i class="bi bi-calendar-check"></i>
                        <span class="nav-label">Consultations</span>
                    </a>
                    <a href="availability.php" class="header-nav-btn">
                        <i class="bi bi-clock"></i>
                        <span class="nav-label">Availability</span>
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

    <!-- Profile Completion Banner -->
    <div id="profileCompletionBanner" style="display: none; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #3b82f6; padding: 20px; margin: 20px auto; max-width: 1400px; border-radius: 12px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 50px; height: 50px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-badge" style="font-size: 24px; color: #3b82f6;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1" style="color: #1e40af; font-weight: 600;">
                            <i class="bi bi-pencil-square me-2"></i>Complete Your Vendor Profile
                        </h5>
                        <p class="mb-0" style="color: #1e3a8a; font-size: 0.95rem;">
                            Help customers learn about your business! Complete your profile with vendor name, description, and contact details before adding products.
                        </p>
                    </div>
                </div>
                <div>
                    <a href="../view/vendor_profile.php" 
                       class="btn" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="bi bi-person-circle"></i>
                        Complete Profile Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <!-- Sales Analytics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: #d1fae5; color: #065f46; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-currency-exchange" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Total Revenue</div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #111827;" id="totalRevenue">GHS 0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: #dbeafe; color: #1e40af; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-bag-check" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Total Orders</div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #111827;" id="totalOrders">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: #fef3c7; color: #92400e; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-box-seam" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Items Sold</div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #111827;" id="itemsSold">0</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: #e9d5ff; color: #6b21a8; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-star" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">Top Product</div>
                                <div style="font-size: 1.1rem; font-weight: 600; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="topProduct">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-buttons">
                    <a href="product.php" class="btn-action btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Product
                    </a>
                    <button class="btn-action btn-secondary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="bi bi-folder-plus me-1"></i> New Category
                    </button>
                    <button class="btn-action btn-secondary" data-bs-toggle="modal" data-bs-target="#brandModal">
                        <i class="bi bi-tags me-1"></i> New Brand
                    </button>
                </div>
                <div class="search-bar">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-input" placeholder="Search my products...">
                    </div>
                    <button class="btn-action btn-primary" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Products Section -->
                <div class="products-section">
                    <div id="productsContainer" class="products-grid">
                        <!-- User products will render here -->
                    </div>
                    <nav id="paginationContainer" class="pagination-wrapper" aria-label="My products pagination"></nav>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Categories Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <h3 class="sidebar-card-title">
                                <i class="bi bi-folder2-open me-2"></i>Categories
                            </h3>
                            <a href="category.php" class="sidebar-link">Manage</a>
                        </div>
                        <ul class="sidebar-list" id="categoriesList">
                            <li class="sidebar-list-item loading">Loading...</li>
                        </ul>
                    </div>

                    <!-- Brands Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <h3 class="sidebar-card-title">
                                <i class="bi bi-tags me-2"></i>Brands
                            </h3>
                            <a href="brand.php" class="sidebar-link">Manage</a>
                        </div>
                        <ul class="sidebar-list" id="brandsList">
                            <li class="sidebar-list-item loading">Loading...</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Category Name</label>
              <input type="text" id="catNameInput" class="form-control" placeholder="e.g. Catering">
            </div>
            <div id="catFeedback" class="small text-danger"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="saveCategoryBtn" class="btn btn-gradient">Save Category</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Brand Modal -->
    <div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Brand</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Brand Name</label>
              <input type="text" id="brandNameInput" class="form-control" placeholder="e.g. Elite Events">
            </div>
            <div id="brandFeedback" class="small text-danger"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="saveBrandBtn" class="btn btn-gradient">Save Brand</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editProductId">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Product Title *</label>
                <input type="text" id="editProductTitle" class="form-control" placeholder="e.g. Premium Catering Package">
              </div>
              <div class="col-md-6">
                <label class="form-label">Price (GHS) *</label>
                <input type="number" id="editProductPrice" class="form-control" placeholder="0.00" step="0.01" min="0">
              </div>
              <div class="col-md-6">
                <label class="form-label">Category *</label>
                <select id="editProductCategory" class="form-select">
                  <option value="">Select Category</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand *</label>
                <select id="editProductBrand" class="form-select">
                  <option value="">Select Brand</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea id="editProductDesc" class="form-control" rows="3" placeholder="Describe your product..."></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Keywords (comma-separated)</label>
                <input type="text" id="editProductKeywords" class="form-control" placeholder="e.g. catering, food, event">
              </div>
            </div>
            <div id="editProductFeedback" class="small text-danger mt-2"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="saveEditProductBtn" class="btn btn-gradient">Save Changes</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/admin_dashboard.js"></script>
    <script>
        // Check subscription status and block actions if pending
        const subscriptionPending = <?php echo (isset($_SESSION['subscription_status']) && $_SESSION['subscription_status'] === 'pending') ? 'true' : 'false'; ?>;
        let profileIncomplete = false;
        
        // Check profile completion on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../actions/check_profile_completion_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && !data.is_complete) {
                        profileIncomplete = true;
                        // Show profile completion banner
                        document.getElementById('profileCompletionBanner').style.display = 'block';
                        
                        // Block add product and related actions
                        blockProfileActions();
                    }
                })
                .catch(error => console.error('Error checking profile:', error));
        });
        
        function blockProfileActions() {
            const addProductBtn = document.querySelector('a[href="product.php"]');
            const categoryBtn = document.querySelector('[data-bs-target="#categoryModal"]');
            const brandBtn = document.querySelector('[data-bs-target="#brandModal"]');
            
            function blockAction(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'Complete Your Profile First',
                    html: 'Please complete your vendor profile before adding products.<br><br>' +
                          '<strong>Required:</strong> Vendor Name, About/Description, Contact Number<br>' +
                          '<strong>Recommended:</strong> Profile Picture',
                    confirmButtonText: 'Go to Profile',
                    confirmButtonColor: '#1e4d2b',
                    showCancelButton: true,
                    cancelButtonText: 'Later'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '../view/vendor_profile.php';
                    }
                });
                return false;
            }
            
            if (addProductBtn) {
                addProductBtn.style.opacity = '0.6';
                addProductBtn.style.cursor = 'not-allowed';
                addProductBtn.addEventListener('click', blockAction);
            }
            
            if (categoryBtn) {
                categoryBtn.style.opacity = '0.6';
                categoryBtn.style.cursor = 'not-allowed';
                categoryBtn.addEventListener('click', blockAction);
            }
            
            if (brandBtn) {
                brandBtn.style.opacity = '0.6';
                brandBtn.style.cursor = 'not-allowed';
                brandBtn.addEventListener('click', blockAction);
            }
        }
        
        if (subscriptionPending) {
            // Disable add product buttons and show alert
            document.addEventListener('DOMContentLoaded', function() {
                const addProductBtn = document.querySelector('a[href="product.php"]');
                const categoryBtn = document.querySelector('[data-bs-target="#categoryModal"]');
                const brandBtn = document.querySelector('[data-bs-target="#brandModal"]');
                
                function blockAction(e) {
                    e.preventDefault();
                    alert('Please complete your subscription payment to access this feature.');
                    return false;
                }
                
                if (addProductBtn) {
                    addProductBtn.style.opacity = '0.6';
                    addProductBtn.style.cursor = 'not-allowed';
                    addProductBtn.addEventListener('click', blockAction);
                }
                
                if (categoryBtn) {
                    categoryBtn.style.opacity = '0.6';
                    categoryBtn.style.cursor = 'not-allowed';
                    categoryBtn.addEventListener('click', blockAction);
                }
                
                if (brandBtn) {
                    brandBtn.style.opacity = '0.6';
                    brandBtn.style.cursor = 'not-allowed';
                    brandBtn.addEventListener('click', blockAction);
                }
            });
        }
        
        // Load sales analytics on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadSalesAnalytics();

            // Enable Enter key for Category modal
            document.getElementById('catNameInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('saveCategoryBtn').click();
                }
            });

            // Enable Enter key for Brand modal
            document.getElementById('brandNameInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('saveBrandBtn').click();
                }
            });

            // Enable Enter key for Edit Product modal (except textarea)
            document.getElementById('editProductModal').addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    document.getElementById('saveEditProductBtn').click();
                }
            });
        });

        function loadSalesAnalytics() {
            fetch('../actions/get_planner_sales_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const analytics = data.analytics;
                        document.getElementById('totalRevenue').textContent = 'GHS ' + parseFloat(analytics.total_revenue).toFixed(2);
                        document.getElementById('totalOrders').textContent = analytics.total_orders;
                        document.getElementById('itemsSold').textContent = analytics.total_items_sold;
                        document.getElementById('topProduct').textContent = analytics.top_product || 'N/A';
                        document.getElementById('topProduct').title = analytics.top_product || 'N/A';
                    }
                })
                .catch(error => {
                    console.error('Error loading sales analytics:', error);
                });
        }
    </script>
</body>
</html>
