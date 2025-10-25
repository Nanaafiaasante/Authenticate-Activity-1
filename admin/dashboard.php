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
    <div class="header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="page-title">My Products</div>
                    <div class="small text-muted">Welcome, <?php echo htmlspecialchars($customer_name); ?></div>
                </div>
                <div>
                    <a href="../view/all_products.php" class="btn btn-outline me-2"><i class="bi bi-grid"></i> All Products</a>
                    <a href="../login/logout.php" class="btn btn-outline"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <div class="row g-3 mb-3">
            <div class="col-auto">
                <a href="product.php" class="btn btn-gradient"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#categoryModal"><i class="bi bi-folder-plus me-1"></i> New Category</button>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline" data-bs-toggle="modal" data-bs-target="#brandModal"><i class="bi bi-tags me-1"></i> New Brand</button>
            </div>
            <div class="col-auto ms-auto">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search my products...">
                    <button class="btn btn-gradient" id="searchBtn"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div id="productsContainer" class="row g-3">
                    <!-- User products will render here -->
                </div>
                <nav id="paginationContainer" class="mt-3" aria-label="My products pagination"></nav>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Categories</strong>
                        <a href="category.php" class="text-decoration-none">Manage</a>
                    </div>
                    <ul class="list-group list-group-flush" id="categoriesList">
                        <li class="list-group-item text-muted">Loading...</li>
                    </ul>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Brands</strong>
                        <a href="brand.php" class="text-decoration-none">Manage</a>
                    </div>
                    <ul class="list-group list-group-flush" id="brandsList">
                        <li class="list-group-item text-muted">Loading...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

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
</body>
</html>
