<?php
/**
 * Product Search Results Page
 * Display search results with filter options
 */

session_start();

// Get search query from URL
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/search.css">
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="page-title">
                        <i class="bi bi-search me-2"></i>Search Results
                    </h1>
                    <p class="page-subtitle">
                        <?php if (!empty($search_query)): ?>
                            Searching for: <span class="search-query-display">"<?php echo htmlspecialchars($search_query); ?>"</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="all_products.php" class="btn btn-outline-custom me-2">
                        <i class="bi bi-grid me-1"></i>All Products
                    </a>
                    <a href="../index.php" class="btn btn-outline-custom">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Search Stats -->
        <div class="search-stats" id="searchStats" style="display: none;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="bi bi-info-circle me-2"></i>
                    <strong id="resultsCount">0</strong> results found
                </div>
                <button class="btn btn-sm btn-outline-custom" onclick="window.location.href='all_products.php'">
                    View All Products
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="bi bi-funnel me-2"></i>Narrow Your Search
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="categoryFilter" class="form-label">Category</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="brandFilter" class="form-label">Brand</label>
                    <select class="form-select" id="brandFilter">
                        <option value="">All Brands</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="sortFilter" class="form-label">Sort By</label>
                    <select class="form-select" id="sortFilter">
                        <option value="relevance">Most Relevant</option>
                        <option value="newest">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn btn-outline-custom" id="applyFiltersBtn">
                        <i class="bi bi-check2-circle me-1"></i>Apply Filters
                    </button>
                    <button class="btn btn-outline-custom ms-2" id="clearFiltersBtn">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div id="productsContainer">
            <div class="loading-spinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Searching products...</p>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Search results pagination" id="paginationContainer"></nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const initialSearchQuery = "<?php echo htmlspecialchars($search_query); ?>";
    </script>
    <script src="../js/product_search_result.js"></script>
</body>
</html>
