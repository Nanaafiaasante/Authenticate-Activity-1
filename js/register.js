$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        const customer_name = $('#customer_name').val().trim();
        const customer_email = $('#customer_email').val().trim();
        const customer_pass = $('#customer_pass').val();
        const customer_country = $('#customer_country').val().trim();
        const customer_city = $('#customer_city').val().trim();
        const customer_contact = $('#customer_contact').val().trim();

        // Validation patterns
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const phonePattern = /^[\+]?[1-9][\d]{0,15}$/; // International phone format
        const namePattern = /^[a-zA-Z\s]{2,100}$/; // Letters and spaces only, 2-100 chars
        const locationPattern = /^[a-zA-Z\s]{2,30}$/; // Letters and spaces only, 2-30 chars
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/;

        // Show loading state
        showLoadingState();

        // Validate all fields are filled
        if (!customer_name || !customer_email || !customer_pass || !customer_country || !customer_city || !customer_contact) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields!',
            });
            return;
        }

        // Validate field lengths
        if (customer_name.length > 100) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name',
                text: 'Full name must not exceed 100 characters!',
            });
            return;
        }

        if (customer_email.length > 50) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Email must not exceed 50 characters!',
            });
            return;
        }

        if (customer_country.length > 30) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Country',
                text: 'Country name must not exceed 30 characters!',
            });
            return;
        }

        if (customer_city.length > 30) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid City',
                text: 'City name must not exceed 30 characters!',
            });
            return;
        }

        if (customer_contact.length > 15) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact',
                text: 'Contact number must not exceed 15 characters!',
            });
            return;
        }

        // Validate name format
        if (!namePattern.test(customer_name)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name Format',
                text: 'Full name should contain only letters and spaces, and be between 2-100 characters!',
            });
            return;
        }

        // Validate email format
        if (!emailPattern.test(customer_email)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email Format',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        // Validate password strength
        if (!passwordPattern.test(customer_pass)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });
            return;
        }

        // Validate country format
        if (!locationPattern.test(customer_country)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Country Format',
                text: 'Country should contain only letters and spaces, and be between 2-30 characters!',
            });
            return;
        }

        // Validate city format
        if (!locationPattern.test(customer_city)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid City Format',
                text: 'City should contain only letters and spaces, and be between 2-30 characters!',
            });
            return;
        }

        // Validate phone number format
        if (!phonePattern.test(customer_contact)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone Number',
                text: 'Please enter a valid phone number (digits only, optionally starting with +)!',
            });
            return;
        }

        // Check email availability first
        $.ajax({
            url: '../actions/check_email_availability.php',
            type: 'POST',
            data: { email: customer_email },
            dataType: 'json',
            success: function(response) {
                if (response.available) {
                    // Proceed with registration
                    registerCustomer();
                } else {
                    hideLoadingState();
                    Swal.fire({
                        icon: 'error',
                        title: 'Email Already Exists',
                        text: 'This email address is already registered. Please use a different email or try logging in.',
                    });
                }
            },
            error: function() {
                // If email check fails, proceed with registration (server will handle it)
                registerCustomer();
            }
        });

        function registerCustomer() {
            $.ajax({
                url: '../actions/register_customer_action.php',
                type: 'POST',
                data: {
                    customer_name: customer_name,
                    customer_email: customer_email,
                    customer_pass: customer_pass,
                    customer_country: customer_country,
                    customer_city: customer_city,
                    customer_contact: customer_contact
                },
                dataType: 'json',
                success: function(response) {
                    hideLoadingState();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
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
                            title: 'Registration Failed',
                            text: response.message,
                        });
                    }
                },
                error: function() {
                    hideLoadingState();
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'An error occurred while connecting to the server. Please try again later.',
                    });
                }
            });
        }
    });

    // Loading state functions
    function showLoadingState() {
        $('#register-btn').prop('disabled', true);
        $('.btn-text').addClass('d-none');
        $('.btn-spinner').removeClass('d-none');
    }

    function hideLoadingState() {
        $('#register-btn').prop('disabled', false);
        $('.btn-text').removeClass('d-none');
        $('.btn-spinner').addClass('d-none');
    }

    // Real-time validation feedback
    $('#customer_email').on('blur', function() {
        const email = $(this).val().trim();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        if (email && emailPattern.test(email)) {
            // Check email availability
            $.ajax({
                url: '../actions/check_email_availability.php',
                type: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function(response) {
                    if (!response.available) {
                        $('#customer_email').addClass('is-invalid');
                        if (!$('#email-feedback').length) {
                            $('#customer_email').after('<div id="email-feedback" class="invalid-feedback">This email is already registered.</div>');
                        }
                    } else {
                        $('#customer_email').removeClass('is-invalid').addClass('is-valid');
                        $('#email-feedback').remove();
                    }
                },
                error: function() {
                    // Don't show error to user for real-time validation
                }
            });
        }
    });

    // Remove validation classes on input
    $('input').on('input', function() {
        $(this).removeClass('is-invalid is-valid');
        $(this).siblings('.invalid-feedback').remove();
    });
});