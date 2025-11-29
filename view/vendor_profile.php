<?php
/**
 * Vendor Profile Page
 * Displays vendor information with rating system and editable fields
 */

session_start();

// Get vendor ID from URL or session
$vendor_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0);
$is_own_profile = isset($_SESSION['customer_id']) && $_SESSION['customer_id'] == $vendor_id;

if ($vendor_id === 0) {
    header('Location: all_products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/all_products.css">
    <link rel="stylesheet" href="../css/vendor_profile.css">
</head>
<body>

    <!-- EMERALD GREEN BOTANICALS in all 4 corners -->
    <div class="botanical-tl"></div>
    <div class="botanical-tr"></div>
    <div class="botanical-bl"></div>
    <div class="botanical-br"></div>

    <!-- GOLD RECTANGULAR FRAMES -->
    <div class="gold-frame-tr"></div>
    <div class="gold-frame-bl"></div>

    <!-- SHINY GOLD DOTS scattered -->
    <div class="gold-dot dot-tr1"></div>
    <div class="gold-dot dot-tr2"></div>
    <div class="gold-dot dot-tr3"></div>
    <div class="gold-dot dot-tr4"></div>
    <div class="gold-dot dot-tr5"></div>
    <div class="gold-dot dot-tr6"></div>
    <div class="gold-dot dot-tr7"></div>

    <div class="gold-dot dot-bl1"></div>
    <div class="gold-dot dot-bl2"></div>
    <div class="gold-dot dot-bl3"></div>
    <div class="gold-dot dot-bl4"></div>
    <div class="gold-dot dot-bl5"></div>
    <div class="gold-dot dot-bl6"></div>
    <div class="gold-dot dot-bl7"></div>

    <!-- Header -->
    <div class="header-section">
        <div class="container">
            <!-- Logo -->
            <div class="header-left">
                <a href="../index.php" class="vc-logo">
                    <div class="vc-logo-ring"></div>
                    <div class="vc-logo-text">
                        <div class="vc-logo-main">VendorConnect</div>
                        <div class="vc-logo-sub">GHANA</div>
                    </div>
                </a>
            </div>
            
            <!-- Center - Back Button -->
            <div class="header-center">
                <a href="../admin/dashboard.php" class="btn btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Back to Products
                </a>
            </div>
            
            <!-- Navigation -->
            <div class="header-right">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                    <a href="../admin/dashboard.php" class="btn-header-nav">
                        <i class="bi bi-grid"></i>
                        <span class="btn-nav-label">Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="cart.php" class="btn-header-nav">
                        <span class="cart-icon-wrapper">
                            <i class="bi bi-cart3"></i>
                            <span class="cart-count-badge">0</span>
                        </span>
                        <span class="btn-nav-label">Cart</span>
                    </a>
                <?php endif; ?>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="../login/logout.php" class="btn-header-nav btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="btn-nav-label">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="../login/login.php" class="btn-header-nav">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="btn-nav-label">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container profile-container">
        <div id="profileContainer">
            <div class="loading-spinner">
                <div class="spinner-border" style="color: #1e4d2b;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading profile...</p>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <div class="profile-pic-upload">
                                    <img src="../uploads/default-avatar.jpg" id="previewImage" class="preview-img" alt="Profile">
                                    <input type="file" class="form-control" id="profilePicture" accept="image/*">
                                    <small class="text-muted">Max 2MB. JPG, PNG, GIF</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" id="vendorName" placeholder="Your business name">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullName" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phoneNumber" readonly>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="city" placeholder="Your city">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" placeholder="Your country">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">About</label>
                            <textarea class="form-control" id="about" rows="4" placeholder="Tell customers about your business..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProfileBtn">
                        <i class="bi bi-check-circle me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const vendorId = <?php echo $vendor_id; ?>;
        const isOwnProfile = <?php echo $is_own_profile ? 'true' : 'false'; ?>;
    </script>
    <script src="../js/vendor_profile.js"></script>
</body>
</html>
