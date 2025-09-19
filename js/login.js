$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        const email = $('#email').val().trim();
        const password = $('#password').val();

        // Basic validation
        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Validate email format
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email Format',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Logging in...');

        // Send AJAX request
        $.ajax({
            url: '../actions/login_action.php',
            type: 'POST',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                submitBtn.prop('disabled', false).html(originalText);
                
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: response.message,
                        showConfirmButton: true,
                        confirmButtonText: 'Continue'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect based on user role or to dashboard
                            window.location.href = response.redirect || '../index.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', false).html(originalText);
                
                console.error('Login error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'An error occurred while connecting to the server. Please try again later.',
                });
            }
        });
    });
});