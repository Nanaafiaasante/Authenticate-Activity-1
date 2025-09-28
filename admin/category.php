<?php
/**
 * Admin Category Management Page
 * Provides CRUD interface for category management
 * Only accessible by admin users
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

$page_title = "Category Management";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #faf7f5 0%, #f5f0ed 50%, #f0ebe8 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .admin-header {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(234, 224, 218, 0.6);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            border: 1px solid rgba(234, 224, 218, 0.6);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(139, 118, 108, 0.08);
            background: rgba(255, 255, 255, 0.8);
        }
        
        .btn-primary {
            background-color: #7c9bb5;
            border-color: #7c9bb5;
        }
        
        .btn-primary:hover {
            background-color: #6a8ba3;
            border-color: #6a8ba3;
        }
        
        .btn-danger {
            background-color: #c67c7c;
            border-color: #c67c7c;
        }
        
        .btn-danger:hover {
            background-color: #b86a6a;
            border-color: #b86a6a;
        }
        
        .btn-warning {
            background-color: #d4a574;
            border-color: #d4a574;
        }
        
        .btn-warning:hover {
            background-color: #c6955f;
            border-color: #c6955f;
        }
        
        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #7c9bb5, #a8c5d8);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="bi bi-tags-fill me-2"></i>
                        VendorConnect Ghana
                    </h2>
                </div>
                <div class="col-md-6 text-end">
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

        <!-- Add Category Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add New Category
                </h5>
            </div>
            <div class="card-body">
                <form id="addCategoryForm">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="categoryName" name="cat_name" 
                                   placeholder="Enter category name" required maxlength="100">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus me-1"></i>Add Category
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories List Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Categories List
                </h5>
                <button class="btn btn-outline-primary btn-sm" onclick="loadCategories()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <tr>
                                <td colspan="3" class="text-center">
                                    <div class="loading">
                                        <i class="bi bi-hourglass-split me-2"></i>Loading categories...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="editCategoryId" name="cat_id">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="cat_name" 
                                   required maxlength="100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateCategory()">
                        <i class="bi bi-check me-1"></i>Update Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1">
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
                    <p>Are you sure you want to delete this category?</p>
                    <p class="text-muted small">This action cannot be undone.</p>
                    <input type="hidden" id="deleteCategoryId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>Delete Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/category.js"></script>
    <script>
        // Load categories when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
        });
    </script>
</body>
</html>
