<?php
/**
 * All Products Page
 * Customer-facing product listing with filters and search
 */

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/all_products.css">
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h1 class="page-title">
                        <i class="bi bi-shop me-2"></i>All Products
                    </h1>
                    <p class="page-subtitle">Discover the best wedding planning services</p>
                </div>
                <div class="col-md-5">
                    <div class="header-search-wrapper">
                        <input type="text" class="form-control header-search-input" id="searchInput" placeholder="Search products...">
                        <button class="header-search-btn" type="button" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                        <a href="../admin/dashboard.php" class="btn-outline-custom me-2">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    <?php else: ?>
                        <a href="../index.php" class="btn-outline-custom me-2">
                            <i class="bi bi-house me-1"></i>Home
                        </a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <a href="../login/logout.php" class="btn-outline-custom">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </a>
                    <?php else: ?>
                        <a href="../login/login.php" class="btn-outline-custom me-2">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                        <a href="../login/register.php" class="btn-outline-custom">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Filters -->
            <div class="col-lg-3">
                <div class="filters-sidebar">
                    <div class="sidebar-header">
                        <h5 class="sidebar-title">
                            <i class="bi bi-funnel me-2"></i>Filters
                        </h5>
                        <button class="btn-clear-all" id="clearFiltersBtn">
                            <i class="bi bi-x-circle me-1"></i>Clear All
                        </button>
                    </div>
                    
                    <div class="filter-section">
                        <label class="filter-label">Category</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    
                    <div class="filter-section">
                        <label class="filter-label">Brand</label>
                        <select class="form-select" id="brandFilter">
                            <option value="">All Brands</option>
                        </select>
                    </div>
                    
                    <div class="filter-section">
                        <label class="filter-label">Price Range (GHS)</label>
                        <div class="price-inputs">
                            <input type="number" class="form-control" id="minPrice" placeholder="Min" min="0" step="100">
                            <span class="price-separator">-</span>
                            <input type="number" class="form-control" id="maxPrice" placeholder="Max" min="0" step="100">
                        </div>
                    </div>
                    
                    <button class="btn-apply-filters" id="applyFiltersBtn">
                        <i class="bi bi-check2-circle me-1"></i>Apply Filters
                    </button>
                </div>
            </div>

            <!-- Right Content - Products -->
            <div class="col-lg-9">
                <!-- Toolbar with Results Count and Sort -->
                <div class="toolbar">
                    <div class="results-count" id="resultsCount">
                        Loading products...
                    </div>
                    <div class="sort-wrapper">
                        <label for="sortFilter" class="sort-label">Sort by:</label>
                        <select class="form-select sort-select" id="sortFilter">
                            <option value="newest">Newest First</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="name">Name: A-Z</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="productsContainer">
                    <div class="loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading products...</p>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Product pagination" id="paginationContainer"></nav>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/all_products.js"></script>
</body>
</html>
