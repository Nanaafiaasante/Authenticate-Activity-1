<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}

// Check for logout message
$logout_message = '';
if (isset($_GET['message']) && $_GET['message'] == 'logged_out') {
    $logout_message = '<div class="alert alert-success text-center">You have been successfully logged out.</div>';
}

// Get email from URL parameter (from registration)
$prefill_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
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

    <div class="container login-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center">
                        <h4>Login</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $logout_message; ?>
                        
                        <form method="POST" action="" class="mt-4" id="login-form">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control animate__animated animate__fadeInUp" id="email" name="email" value="<?php echo $prefill_email; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control animate__animated animate__fadeInUp" id="password" name="password" required>
                            </div>
                            <div class="mb-4 text-end">
                                <a href="#" id="forgot-password-link" class="text-muted" style="font-size: 0.9rem; text-decoration: none;">
                                    <i class="fas fa-question-circle me-1"></i>Forgot Password?
                                </a>
                            </div>
                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Don't have an account? <a href="select_role.php" class="highlight">Register here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #C9A961 0%, #D4AF37 100%); color: white; border: none;">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">
                        <i class="fas fa-key me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                    <form id="forgot-password-form">
                        <div class="mb-3">
                            <label for="forgot-email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="forgot-email" name="email" placeholder="your@email.com" required>
                        </div>
                        <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #C9A961, #D4AF37); color: white; font-weight: 600;">
                            <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/login.js"></script>
    <script>
        $(document).ready(function() {
            // Show forgot password modal
            $('#forgot-password-link').click(function(e) {
                e.preventDefault();
                const forgotModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
                forgotModal.show();
            });
            
            // Handle forgot password form submission
            $('#forgot-password-form').submit(function(e) {
                e.preventDefault();
                
                const email = $('#forgot-email').val().trim();
                
                if (!email) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Required',
                        text: 'Please enter your email address.',
                    });
                    return;
                }
                
                if (!email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.',
                    });
                    return;
                }
                
                // Show loading
                Swal.fire({
                    title: 'Sending Reset Link...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send request
                $.ajax({
                    url: '../actions/forgot_password_action.php',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function(response) {
                        // Close modal
                        const modalEl = document.getElementById('forgotPasswordModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        
                        if (response.status === 'success') {
                            // Redirect directly to reset password page with token
                            Swal.fire({
                                icon: 'success',
                                title: 'Email Verified!',
                                text: 'Redirecting to password reset...',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'reset_password.php?token=' + response.reset_token;
                            });
                            
                            // Clear form
                            $('#forgot-password-form')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to verify email. Please try again.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Forgot password error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'An error occurred while connecting to the server. Please try again.',
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>