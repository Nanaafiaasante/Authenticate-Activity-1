<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get role from URL parameter
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Validate role
if (!in_array($role, ['couple', 'planner'])) {
    header('Location: select_role.php');
    exit();
}

// For planners, validate subscription tier and payment
$subscription_tier = null;
$payment_reference = null;
$planner_email = null;
$planner_name = null;

if ($role === 'planner') {
    $tier = isset($_GET['tier']) ? $_GET['tier'] : '';
    
    // Validate tier
    if (!in_array($tier, ['starter', 'premium'])) {
        header('Location: select_subscription.php?role=planner');
        exit();
    }
    
    $subscription_tier = $tier;
}

// Set user_role value: 2 for couple (customer), 1 for planner (admin/vendor)
$user_role = ($role === 'planner') ? 1 : 2;
$role_display = ucfirst($role);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/register.css">
</head>

<body>
    <!-- VENDORCONNECT GHANA LOGO -->
    <a href="../index.php" class="vc-logo">
        <div class="vc-logo-ring"></div>
        <div class="vc-logo-text">
            <div class="vc-logo-main">VendorConnect</div>
            <div class="vc-logo-sub">GHANA</div>
        </div>
    </a>

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

    <div class="container register-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                                    <div class="card-header text-center">
                    <h1 class="mb-2">Register as <?php echo $role === 'couple' ? 'Couple' : 'Planner'; ?></h1>
                    <p class="text-muted">
                        <?php 
                        if ($role === 'couple') {
                            echo 'Create your account to start planning your special day';
                        } else {
                            echo 'Join our network of professional wedding planners';
                            if ($subscription_tier) {
                                $tier_name = ($subscription_tier === 'premium') ? 'Premium (GHS 199/mo)' : 'Basic (GHS 99/mo)';
                                echo ' - ' . $tier_name . ' Plan';
                            }
                        }
                        ?>
                    </p>
                </div>
                    <div class="card-body">
                        <?php if ($role === 'planner'): ?>
                        <!-- Subscription Info -->
                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="fa fa-info-circle fa-2x me-3"></i>
                            <div>
                                <strong>Complete your registration</strong><br>
                                <small>You'll be able to activate your subscription after registration</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" id="register-form">
                            <!-- Hidden field for user role -->
                            <input type="hidden" name="user_role" value="<?php echo $user_role; ?>" />
                            <?php if ($subscription_tier): ?>
                            <!-- Hidden field for subscription tier -->
                            <input type="hidden" name="subscription_tier" value="<?php echo $subscription_tier; ?>" />
                            <!-- Subscription status set to pending until payment -->
                            <input type="hidden" name="subscription_status" value="pending" />
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label"><i class="fa fa-user"></i> Full Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required maxlength="100" 
                                           pattern="[A-Za-z\s'\-]+" title="Name should only contain letters, spaces, hyphens, and apostrophes"
                                           placeholder="Enter your name"
                                           value="<?php echo htmlspecialchars($planner_name ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label"><i class="fa fa-envelope"></i> Email</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required maxlength="50">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_pass" class="form-label"><i class="fa fa-lock"></i> Password</label>
                                    <input type="password" class="form-control" id="customer_pass" name="customer_pass" required maxlength="150">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_pass_confirm" class="form-label"><i class="fa fa-lock"></i> Confirm Password</label>
                                    <input type="password" class="form-control" id="customer_pass_confirm" name="customer_pass_confirm" required maxlength="150">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_contact" class="form-label"><i class="fa fa-phone"></i> Phone Number</label>
                                <input type="tel" class="form-control" id="customer_contact" name="customer_contact" required 
                                       placeholder="+233 XX XXX XXXX" maxlength="20"
                                       pattern="\+[0-9]{1,4}\s?[0-9\s]{6,15}" 
                                       title="Please enter a valid international phone number (e.g., +233 24 123 4567)">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fa fa-map-marker-alt"></i> Location</label>
                                <div class="location-input-wrapper">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <select class="form-select" id="customer_country" name="customer_country" required>
                                                <option value="">Select Country</option>
                                                <option value="Ghana" data-iso2="GH" selected>Ghana 🇬🇭</option>
                                            </select>
                                            <small class="text-muted d-block mt-1" id="country-loading">
                                                <i class="fa fa-spinner fa-spin"></i> Loading countries...
                                            </small>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <select class="form-select" id="customer_city" name="customer_city" required disabled>
                                                <option value="">Select city first</option>
                                            </select>
                                            <small class="text-muted d-block mt-1" id="city-loading" style="display: none;">
                                                <i class="fa fa-spinner fa-spin"></i> Loading cities...
                                            </small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-2" id="use-current-location">
                                        <i class="fa fa-location-arrow me-2"></i>Use Current Location
                                    </button>
                                    <small class="text-muted d-block mt-1" id="location-status"></small>
                                </div>
                            </div>
                            
                            
                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom" id="register-btn">
                                <span class="btn-text">Register</span>
                                <span class="btn-spinner d-none">
                                    <i class="fa fa-spinner fa-spin"></i> Registering...
                                </span>
                            </button>
                        </form>
                    </div>
                                    <div class="card-footer text-center">
                    <p>Already have an account? <a href="login.php" class="highlight">Login here</a></p>
                    <p>
                        <a href="<?php echo ($role === 'planner') ? 'select_subscription.php?role=planner' : 'select_role.php'; ?>" class="text-muted">
                            ← Back to <?php echo ($role === 'planner') ? 'subscription selection' : 'role selection'; ?>
                        </a>
                    </p>
                </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/register.js"></script>
    <script>
        // Ghana phone number formatting and validation
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('customer_contact');
            const nameInput = document.getElementById('customer_name');
            
            // Phone number formatting (flexible for different country codes)
            if (phoneInput) {
                // Ensure phone starts with + when focused
                phoneInput.addEventListener('focus', function() {
                    if (this.value === '' || !this.value.startsWith('+')) {
                        // Default will be set by country selection
                        if (!this.value.startsWith('+')) {
                            this.value = '+233 ';
                        }
                    }
                });
                
                phoneInput.addEventListener('input', function(e) {
                    let value = this.value;
                    
                    // Ensure it starts with +
                    if (!value.startsWith('+')) {
                        value = '+' + value.replace(/^\+/, '');
                    }
                    
                    // Allow only digits after the +
                    const parts = value.split(' ');
                    const code = parts[0]; // e.g., +233
                    const rest = value.substring(code.length).replace(/\D/g, '');
                    
                    // Format with spaces for readability
                    if (rest.length === 0) {
                        value = code + ' ';
                    } else if (rest.length <= 3) {
                        value = code + ' ' + rest;
                    } else if (rest.length <= 6) {
                        value = code + ' ' + rest.substring(0, 3) + ' ' + rest.substring(3);
                    } else {
                        value = code + ' ' + rest.substring(0, 3) + ' ' + rest.substring(3, 6) + ' ' + rest.substring(6, 10);
                    }
                    
                    this.value = value;
                });
                
                // Prevent deletion of the + sign
                phoneInput.addEventListener('keydown', function(e) {
                    const cursorPos = this.selectionStart;
                    if ((e.key === 'Backspace' || e.key === 'Delete') && cursorPos <= 1) {
                        e.preventDefault();
                    }
                });
                
                // Basic validation on blur
                phoneInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    // Check if it has a country code and at least some digits
                    if (value && value.length > 0) {
                        const digits = value.replace(/\D/g, '');
                        if (digits.length < 8) {
                            this.setCustomValidity('Please enter a complete phone number');
                        } else {
                            this.setCustomValidity('');
                        }
                    }
                });
            }
            
            // Name validation - allow letters, spaces, hyphens, and apostrophes
            if (nameInput) {
                nameInput.addEventListener('input', function(e) {
                    // Remove any characters that aren't letters, spaces, hyphens, or apostrophes
                    this.value = this.value.replace(/[^A-Za-z\s'-]/g, '');
                    
                    // Capitalize first letter of each word
                    this.value = this.value.replace(/\b\w/g, function(char) {
                        return char.toUpperCase();
                    });
                });
                
                nameInput.addEventListener('blur', function() {
                    // Trim and validate
                    this.value = this.value.trim();
                    if (this.value.length < 2) {
                        this.setCustomValidity('Name must be at least 2 characters long');
                    } else if (!/^[A-Za-z\s'-]+$/.test(this.value)) {
                        this.setCustomValidity('Name should only contain letters, spaces, hyphens, and apostrophes');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>

</html>