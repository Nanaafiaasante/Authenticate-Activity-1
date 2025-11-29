<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}

// Get token from URL
$token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';

if (empty($token)) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - VendorConnect Ghana</title>
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
                        <h4><i class="fas fa-key me-2"></i>Reset Your Password</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-center text-muted mb-4">Enter your new password below.</p>
                        
                        <form method="POST" action="" class="mt-4" id="reset-password-form">
                            <input type="hidden" id="reset-token" name="token" value="<?php echo $token; ?>">
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password <i class="fa fa-lock"></i></label>
                                <div class="input-group">
                                    <input type="password" class="form-control animate__animated animate__fadeInUp" id="new_password" name="new_password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Must be at least 6 characters with uppercase, lowercase, and a number.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password <i class="fa fa-lock"></i></label>
                                <div class="input-group">
                                    <input type="password" class="form-control animate__animated animate__fadeInUp" id="confirm_password" name="confirm_password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">
                                <i class="fas fa-check-circle me-2"></i>Reset Password
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Remember your password? <a href="login.php" class="highlight">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#toggleNewPassword').click(function() {
                const passwordInput = $('#new_password');
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            $('#toggleConfirmPassword').click(function() {
                const passwordInput = $('#confirm_password');
                const icon = $(this).find('i');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
            // Handle form submission
            $('#reset-password-form').submit(function(e) {
                e.preventDefault();
                
                const token = $('#reset-token').val();
                const new_password = $('#new_password').val().trim();
                const confirm_password = $('#confirm_password').val().trim();
                
                // Client-side validation
                if (!new_password || !confirm_password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: 'Please fill in all fields!',
                    });
                    return;
                }
                
                if (new_password !== confirm_password) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Passwords Don\'t Match',
                        text: 'Please make sure both password fields match!',
                    });
                    return;
                }
                
                if (new_password.length < 6) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password Too Short',
                        text: 'Password must be at least 6 characters long!',
                    });
                    return;
                }
                
                const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/;
                if (!passwordPattern.test(new_password)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Weak Password',
                        text: 'Password must contain at least one lowercase letter, one uppercase letter, and one number!',
                    });
                    return;
                }
                
                // Show loading state
                Swal.fire({
                    title: 'Resetting Password...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit reset request
                $.ajax({
                    url: '../actions/reset_password_action.php',
                    type: 'POST',
                    data: {
                        token: token,
                        new_password: new_password,
                        confirm_password: confirm_password
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Reset Successful!',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonText: 'Go to Login'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'login.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Reset Failed',
                                text: response.message || 'Failed to reset password. Please try again.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Reset error:', error);
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
