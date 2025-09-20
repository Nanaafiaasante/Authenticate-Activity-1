<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .btn-custom {
            background-color: #D19C97;
            border-color: #D19C97;
            color: #fff;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #b77a7a;
            border-color: #b77a7a;
        }

        .highlight {
            color: #D19C97;
            transition: color 0.3s;
        }

        .highlight:hover {
            color: #b77a7a;
        }

        body {
            /* Base background color */
            background-color: #f8f9fa;

            /* Gradient-like grid using repeating-linear-gradients */
            background-image:
                repeating-linear-gradient(0deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                repeating-linear-gradient(90deg,
                    #b77a7a,
                    #b77a7a 1px,
                    transparent 1px,
                    transparent 20px),
                linear-gradient(rgba(183, 122, 122, 0.1),
                    rgba(183, 122, 122, 0.1));

            /* Blend the gradients for a subtle overlay effect */
            background-blend-mode: overlay;

            /* Define the size of the grid */
            background-size: 20px 20px;

            /* Ensure the background covers the entire viewport */
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .register-container {
            margin-top: 50px;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #D19C97;
            color: #fff;
        }

        .custom-radio .form-check-input:checked+.form-check-label::before {
            background-color: #D19C97;
            border-color: #D19C97;
        }

        .form-check-label {
            position: relative;
            padding-left: 2rem;
            cursor: pointer;
        }

        .form-check-label::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
            border: 2px solid #D19C97;
            border-radius: 50%;
            background-color: #fff;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .form-check-input:focus+.form-check-label::before {
            box-shadow: 0 0 0 0.2rem rgba(209, 156, 151, 0.5);
        }

        .animate-pulse-custom {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="container register-container">
        <div class="row justify-content-center animate__animated animate__fadeInDown">
            <div class="col-md-6">
                <div class="card animate__animated animate__zoomIn">
                    <div class="card-header text-center highlight">
                        <h4>Register</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="mt-4" id="register-form">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Full Name <i class="fa fa-user"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="customer_name" name="customer_name" required maxlength="100">
                                <div class="form-text">Maximum 100 characters</div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                                <input type="email" class="form-control animate__animated animate__fadeInUp" id="customer_email" name="customer_email" required maxlength="50">
                                <div class="form-text">Must be unique and valid email format</div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_pass" class="form-label">Password <i class="fa fa-lock"></i></label>
                                <input type="password" class="form-control animate__animated animate__fadeInUp" id="customer_pass" name="customer_pass" required maxlength="150">
                                <div class="form-text">At least 6 characters with uppercase, lowercase, and number</div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_country" class="form-label">Country <i class="fa fa-globe"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="customer_country" name="customer_country" required maxlength="30">
                                <div class="form-text">Maximum 30 characters</div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_city" class="form-label">City <i class="fa fa-map-marker"></i></label>
                                <input type="text" class="form-control animate__animated animate__fadeInUp" id="customer_city" name="customer_city" required maxlength="30">
                                <div class="form-text">Maximum 30 characters</div>
                            </div>
                            <div class="mb-3">
                                <label for="customer_contact" class="form-label">Contact Number <i class="fa fa-phone"></i></label>
                                <input type="tel" class="form-control animate__animated animate__fadeInUp" id="customer_contact" name="customer_contact" required maxlength="15">
                                <div class="form-text">Valid phone number format</div>
                            </div>
                            <div class="mb-4">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle"></i> You will be registered as a Customer by default.
                                </small>
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
                        Already have an account? <a href="login.php" class="highlight">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/register.js"></script>
</body>

</html>