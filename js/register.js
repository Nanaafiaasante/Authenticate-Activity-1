$(document).ready(function() {
    // Load countries from REST Countries API
    loadCountries();
    
    function loadCountries() {
        const $countrySelect = $('#customer_country');
        const $loadingMsg = $('#country-loading');
        
        // Store country data globally for phone code lookup
        window.countryData = {};
        
        fetch('https://restcountries.com/v3.1/all?fields=name,flag,idd,cca2')
            .then(response => {
                if (!response.ok) {
                    throw new Error('API request failed');
                }
                return response.json();
            })
            .then(data => {
                // Ensure data is an array
                if (!Array.isArray(data)) {
                    throw new Error('Invalid response format');
                }
                
                // Sort countries alphabetically
                data.sort((a, b) => {
                    const nameA = a.name.common.toUpperCase();
                    const nameB = b.name.common.toUpperCase();
                    return nameA < nameB ? -1 : nameA > nameB ? 1 : 0;
                });
                
                // Clear loading message
                $loadingMsg.hide();
                
                // Clear existing options except the first one
                $countrySelect.find('option:not(:first)').remove();
                
                // Add Ghana first (default for this app)
                const ghanaData = data.find(c => c.name.common === 'Ghana');
                const ghanaCode = ghanaData && ghanaData.idd ? `${ghanaData.idd.root}${ghanaData.idd.suffixes ? ghanaData.idd.suffixes[0] : ''}` : '+233';
                window.countryData['Ghana'] = ghanaCode;
                $countrySelect.append('<option value="Ghana" data-code="' + ghanaCode + '" selected>Ghana 🇬🇭</option>');
                
                // Add all other countries
                data.forEach(country => {
                    const countryName = country.name.common;
                    const flag = country.flag || '';
                    const root = country.idd?.root || '';
                    const suffix = country.idd?.suffixes ? country.idd.suffixes[0] : '';
                    const phoneCode = root + suffix;
                    const iso2 = country.cca2 || ''; // ISO 3166-1 alpha-2 code
                    
                    // Store phone code
                    if (phoneCode) {
                        window.countryData[countryName] = phoneCode;
                    }
                    
                    // Skip Ghana since we already added it
                    if (countryName !== 'Ghana') {
                        $countrySelect.append(`<option value="${countryName}" data-code="${phoneCode}" data-iso2="${iso2}">${countryName} ${flag}</option>`);
                    }
                });
                
                // Set Ghana's phone code by default and load cities
                updatePhonePrefix('Ghana');
                loadCitiesForCountry('GH', 'Ghana');
            })
            .catch(error => {
                console.error('Error loading countries:', error);
                $loadingMsg.text('Using default country list.').css('color', '#6c757d');
                
                // Fallback to a comprehensive list if API fails with phone codes and ISO codes
                const fallbackCountries = [
                    {name: 'Ghana', code: '+233', iso2: 'GH', flag: '🇬🇭'},
                    {name: 'Nigeria', code: '+234', iso2: 'NG'},
                    {name: 'South Africa', code: '+27', iso2: 'ZA'},
                    {name: 'Kenya', code: '+254', iso2: 'KE'},
                    {name: 'Egypt', code: '+20', iso2: 'EG'},
                    {name: 'United States', code: '+1', iso2: 'US'},
                    {name: 'United Kingdom', code: '+44', iso2: 'GB'},
                    {name: 'Canada', code: '+1', iso2: 'CA'},
                    {name: 'Australia', code: '+61', iso2: 'AU'},
                    {name: 'Germany', code: '+49', iso2: 'DE'},
                    {name: 'France', code: '+33', iso2: 'FR'},
                    {name: 'Spain', code: '+34', iso2: 'ES'},
                    {name: 'Italy', code: '+39', iso2: 'IT'},
                    {name: 'India', code: '+91', iso2: 'IN'},
                    {name: 'China', code: '+86', iso2: 'CN'},
                    {name: 'Japan', code: '+81', iso2: 'JP'}
                ];
                
                // Store fallback data
                window.countryData = {};
                fallbackCountries.forEach(c => {
                    window.countryData[c.name] = c.code;
                });
                
                // Clear and repopulate with fallback
                $countrySelect.find('option:not(:first)').remove();
                fallbackCountries.forEach(country => {
                    const isGhana = country.name === 'Ghana';
                    const flag = country.flag || '';
                    if (isGhana) {
                        $countrySelect.append(`<option value="${country.name}" data-code="${country.code}" data-iso2="${country.iso2}" selected>${country.name} ${flag}</option>`);
                    } else {
                        $countrySelect.append(`<option value="${country.name}" data-code="${country.code}" data-iso2="${country.iso2}">${country.name} ${flag}</option>`);
                    }
                });
                
                // Set Ghana's phone code by default and load cities
                updatePhonePrefix('Ghana');
                loadCitiesForCountry('GH', 'Ghana');
                
                setTimeout(() => $loadingMsg.hide(), 3000);
            });
    }
    
    // Function to update phone prefix based on selected country
    function updatePhonePrefix(countryName) {
        const phoneCode = window.countryData[countryName];
        if (phoneCode) {
            const $phoneInput = $('#customer_contact');
            const currentValue = $phoneInput.val();
            
            // Update placeholder with country-specific format
            const placeholderExamples = {
                'Ghana': phoneCode + ' 24 123 4567',
                'Nigeria': phoneCode + ' 802 123 4567',
                'United States': phoneCode + ' 555 123 4567',
                'United Kingdom': phoneCode + ' 7700 900123',
                'South Africa': phoneCode + ' 71 123 4567',
                'Kenya': phoneCode + ' 712 345678',
                'default': phoneCode + ' XX XXX XXXX'
            };
            
            const placeholder = placeholderExamples[countryName] || placeholderExamples['default'];
            $phoneInput.attr('placeholder', placeholder);
            
            // Update title for better UX
            $phoneInput.attr('title', `Please enter a valid ${countryName} phone number starting with ${phoneCode}`);
            
            // Only update if field is empty or just has a prefix
            if (!currentValue || currentValue.match(/^\+\d{0,4}\s?$/)) {
                $phoneInput.val(phoneCode + ' ');
            }
        }
    }
    
    // Function to load cities for selected country
    function loadCitiesForCountry(iso2Code, countryName) {
        const $citySelect = $('#customer_city');
        const $loadingMsg = $('#city-loading');
        
        if (!iso2Code) {
            $citySelect.prop('disabled', false).html('<option value="">Select City</option>');
            return;
        }
        
        // Show loading
        $citySelect.prop('disabled', true).html('<option value="">Loading cities...</option>');
        $loadingMsg.show();
        
        // Use curated fallback list (faster and more reliable)
        setTimeout(() => {
            loadCitiesFallback(countryName);
            $loadingMsg.hide();
        }, 300);
    }
    
    // Fallback city list for major countries
    function loadCitiesFallback(countryName) {
        const $citySelect = $('#customer_city');
        const $cityTextInput = $('#customer_city_text');
        
        const citiesByCountry = {
            'Ghana': ['Accra', 'Kumasi', 'Tamale', 'Sekondi-Takoradi', 'Ashaiman', 'Sunyani', 'Cape Coast', 'Obuasi', 'Teshie', 'Tema', 'Madina', 'Koforidua', 'Wa', 'Techiman'],
            'Nigeria': ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt', 'Benin City', 'Kaduna', 'Maiduguri', 'Enugu', 'Zaria', 'Ilorin', 'Jos', 'Onitsha', 'Aba', 'Warri'],
            'United States': ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'San Jose', 'Austin', 'Jacksonville', 'Fort Worth', 'Columbus', 'Charlotte', 'San Francisco', 'Indianapolis', 'Seattle', 'Denver', 'Boston', 'Portland', 'Las Vegas', 'Miami', 'Atlanta'],
            'United Kingdom': ['London', 'Birmingham', 'Manchester', 'Glasgow', 'Liverpool', 'Edinburgh', 'Leeds', 'Bristol', 'Sheffield', 'Leicester', 'Newcastle', 'Nottingham', 'Southampton', 'Cardiff', 'Belfast', 'Brighton', 'Oxford', 'Cambridge'],
            'South Africa': ['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth', 'Bloemfontein', 'East London', 'Pietermaritzburg', 'Nelspruit', 'Polokwane', 'Kimberley'],
            'Kenya': ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Naivasha', 'Kitale', 'Garissa'],
            'Egypt': ['Cairo', 'Alexandria', 'Giza', 'Shubra El Kheima', 'Port Said', 'Suez', 'Luxor', 'Aswan', 'Mansoura', 'Tanta'],
            'Canada': ['Toronto', 'Montreal', 'Vancouver', 'Calgary', 'Edmonton', 'Ottawa', 'Winnipeg', 'Quebec City', 'Hamilton', 'Kitchener', 'London', 'Victoria', 'Halifax'],
            'Australia': ['Sydney', 'Melbourne', 'Brisbane', 'Perth', 'Adelaide', 'Gold Coast', 'Newcastle', 'Canberra', 'Wollongong', 'Hobart', 'Darwin'],
            'Germany': ['Berlin', 'Hamburg', 'Munich', 'Cologne', 'Frankfurt', 'Stuttgart', 'Düsseldorf', 'Dortmund', 'Essen', 'Leipzig', 'Bremen', 'Dresden', 'Hanover', 'Nuremberg'],
            'France': ['Paris', 'Marseille', 'Lyon', 'Toulouse', 'Nice', 'Nantes', 'Strasbourg', 'Montpellier', 'Bordeaux', 'Lille', 'Rennes', 'Reims', 'Saint-Étienne'],
            'Spain': ['Madrid', 'Barcelona', 'Valencia', 'Seville', 'Zaragoza', 'Málaga', 'Murcia', 'Palma', 'Las Palmas', 'Bilbao', 'Alicante', 'Córdoba', 'Valladolid'],
            'Italy': ['Rome', 'Milan', 'Naples', 'Turin', 'Palermo', 'Genoa', 'Bologna', 'Florence', 'Bari', 'Catania', 'Venice', 'Verona', 'Messina', 'Padua'],
            'India': ['Mumbai', 'Delhi', 'Bangalore', 'Hyderabad', 'Chennai', 'Kolkata', 'Pune', 'Ahmedabad', 'Jaipur', 'Surat', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Bhopal', 'Visakhapatnam', 'Vadodara', 'Ludhiana', 'Agra', 'Nashik'],
            'China': ['Beijing', 'Shanghai', 'Guangzhou', 'Shenzhen', 'Chengdu', 'Hangzhou', 'Wuhan', 'Xian', 'Chongqing', 'Tianjin', 'Nanjing', 'Shenyang', 'Harbin', 'Qingdao'],
            'Japan': ['Tokyo', 'Yokohama', 'Osaka', 'Nagoya', 'Sapporo', 'Fukuoka', 'Kobe', 'Kyoto', 'Kawasaki', 'Saitama', 'Hiroshima', 'Sendai', 'Kitakyushu', 'Chiba']
        };
        
        const cities = citiesByCountry[countryName] || [];
        
        // Show dropdown, hide text input by default
        $citySelect.removeClass('d-none').prop('disabled', false);
        if ($cityTextInput.length) {
            $cityTextInput.addClass('d-none').prop('required', false);
        }
        
        $citySelect.html('<option value="">Select City</option>');
        
        if (cities.length > 0) {
            cities.forEach(city => {
                $citySelect.append(`<option value="${city}">${city}</option>`);
            });
            // Add an "Other" option for cities not in the list
            $citySelect.append('<option value="Other">Other (Type manually)</option>');
        } else {
            // For countries without predefined cities, add option to type
            $citySelect.append('<option value="Other">Type city name</option>');
        }
    }
    
    // Listen for country selection changes
    $(document).on('change', '#customer_country', function() {
        const selectedCountry = $(this).val();
        const iso2Code = $(this).find('option:selected').data('iso2');
        
        if (selectedCountry) {
            updatePhonePrefix(selectedCountry);
            loadCitiesForCountry(iso2Code, selectedCountry);
        }
    });
    
    // Listen for city selection changes to show text input if "Other" is selected
    $(document).on('change', '#customer_city', function() {
        const selectedCity = $(this).val();
        const $cityTextInput = $('#customer_city_text');
        
        if (selectedCity === 'Other') {
            // Show text input, hide dropdown
            if ($cityTextInput.length === 0) {
                // Create text input if it doesn't exist
                $(this).after(`
                    <input type="text" class="form-control mt-2" id="customer_city_text" 
                           name="customer_city_text" placeholder="Enter your city" 
                           maxlength="30" required>
                `);
            } else {
                $cityTextInput.removeClass('d-none').prop('required', true);
            }
            $(this).prop('required', false);
        } else {
            // Hide text input if it exists
            if ($cityTextInput.length) {
                $cityTextInput.addClass('d-none').prop('required', false).val('');
            }
            $(this).prop('required', true);
        }
    });
    
    // Use Current Location functionality
    $('#use-current-location').click(function() {
        const $btn = $(this);
        const $status = $('#location-status');
        const originalText = $btn.html();
        
        // Show explanation before requesting permission
        Swal.fire({
            icon: 'question',
            title: 'Enable Location Access?',
            html: `
                <p>We'd like to use your current location to:</p>
                <ul style="text-align: left; padding-left: 20px;">
                    <li>Automatically fill in your city and country</li>
                    <li>Show you services and vendors near you</li>
                    <li>Help you discover local wedding planners</li>
                </ul>
                <p><small>Your browser will ask for permission. Click "Allow" to continue.</small></p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Enable Location',
            cancelButtonText: 'No, thanks',
            confirmButtonColor: '#C9A961',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // User agreed, now request location
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Getting location...');
                $status.text('Requesting location permission...');
                
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Use reverse geocoding to get city and country
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    // Use our backend proxy to avoid CORS issues
                    fetch(`../actions/reverse_geocode_action.php?lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            // Check for error response
                            if (data.error) {
                                throw new Error(data.message || 'Geocoding failed');
                            }
                            const address = data.address;
                            const city = address.city || address.town || address.village || address.county || '';
                            const country = address.country || '';
                            
                            if (city && country) {
                                // Try to match and select the country in the dropdown
                                const $countrySelect = $('#customer_country');
                                const $citySelect = $('#customer_city');
                                const countryOption = $countrySelect.find(`option[value="${country}"]`);
                                
                                if (countryOption.length > 0) {
                                    $countrySelect.val(country).trigger('change');
                                    
                                    // Wait for cities to load, then select city
                                    setTimeout(() => {
                                        const cityOption = $citySelect.find(`option[value="${city}"]`);
                                        if (cityOption.length > 0) {
                                            $citySelect.val(city);
                                            $status.text(`✓ Location set: ${city}, ${country}`).css('color', '#28a745');
                                        } else {
                                            // City not in dropdown, keep it in mind for manual entry
                                            $status.text(`✓ Country set to ${country}. Please select your city.`).css('color', '#ffc107');
                                        }
                                    }, 1500);
                                } else {
                                    // Country not found in dropdown, try partial match
                                    let matched = false;
                                    $countrySelect.find('option').each(function() {
                                        if ($(this).val().toLowerCase().includes(country.toLowerCase()) || 
                                            country.toLowerCase().includes($(this).val().toLowerCase())) {
                                            $countrySelect.val($(this).val());
                                            matched = true;
                                            return false;
                                        }
                                    });
                                    
                                    if (matched) {
                                        $status.text(`✓ City set: ${city}. Please verify country.`).css('color', '#ffc107');
                                    } else {
                                        $status.text(`✓ City set: ${city}. Country not found, please select manually.`).css('color', '#ffc107');
                                    }
                                }
                            } else {
                                $status.text('Could not determine location. Please enter manually.').css('color', '#dc3545');
                            }
                            
                            $btn.prop('disabled', false).html(originalText);
                        })
                        .catch(error => {
                            console.error('Geocoding error:', error);
                            $status.text('Could not determine location. Please enter manually.').css('color', '#dc3545');
                            $btn.prop('disabled', false).html(originalText);
                        });
                },
                function(error) {
                    let errorMessage = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enter manually.';
                            
                            // Show SweetAlert explaining why location is needed
                            Swal.fire({
                                icon: 'info',
                                title: 'Enable Location Access',
                                html: `
                                    <p>We need your location to show you services near you.</p>
                                    <p><strong>How to enable location:</strong></p>
                                    <ol style="text-align: left; padding-left: 20px;">
                                        <li>Click the location icon <i class="fa fa-location-arrow"></i> in your browser's address bar</li>
                                        <li>Select "Allow" or "Always allow"</li>
                                        <li>Click the "Use Current Location" button again</li>
                                    </ol>
                                    <p><small>Or you can manually enter your city and country below.</small></p>
                                `,
                                confirmButtonText: 'Got it',
                                confirmButtonColor: '#C9A961'
                            });
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                        default:
                            errorMessage = 'Unknown error occurred.';
                            break;
                    }
                    $status.text(errorMessage).css('color', '#dc3545');
                    $btn.prop('disabled', false).html(originalText);
                }
            );
                } else {
                    $status.text('Geolocation is not supported by your browser.').css('color', '#dc3545');
                    $btn.prop('disabled', false).html(originalText);
                }
            } else {
                // User cancelled, reset button
                $btn.prop('disabled', false).html(originalText);
                $status.text('You can manually enter your location below.').css('color', '#6c757d');
            }
        });
    });
    
    $('#register-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        const customer_name = $('#customer_name').val().trim();
        const customer_email = $('#customer_email').val().trim();
        const customer_pass = $('#customer_pass').val();
        const customer_pass_confirm = $('#customer_pass_confirm').val();
        const customer_country = $('#customer_country').val().trim();
        let customer_city = $('#customer_city').val().trim();
        // If "Other" is selected, use the text input value
        if (customer_city === 'Other') {
            customer_city = $('#customer_city_text').val().trim();
        }
        const customer_contact = $('#customer_contact').val().trim();

        // Validation patterns
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        const phonePattern = /^[\+]?[1-9][\d\s]{0,20}$/; // International phone format with spaces allowed
        // Allow letters, spaces, hyphens and apostrophes (e.g., "Mary-Anne O'Connor")
        const namePattern = /^[a-zA-Z\s'\-]{2,100}$/; // Letters, spaces, hyphens, apostrophes, 2-100 chars
        const locationPattern = /^[a-zA-Z\s]{2,30}$/; // Letters and spaces only, 2-30 chars
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/;

        // Show loading state
        showLoadingState();

        // Validate all fields are filled
        if (!customer_name || !customer_email || !customer_pass || !customer_pass_confirm || !customer_country || !customer_city || !customer_contact) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Missing Information',
                text: 'Please fill in all required fields!',
            });
            return;
        }
        
        // Check if passwords match
        if (customer_pass !== customer_pass_confirm) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Passwords Don\'t Match',
                text: 'Please make sure both password fields match!',
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

        // Remove spaces from contact number for length validation
        const contactWithoutSpaces = customer_contact.replace(/\s/g, '');
        if (contactWithoutSpaces.length > 15) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Contact',
                text: 'Contact number must not exceed 15 characters (excluding spaces)!',
            });
            return;
        }

        // Validate name format
        if (!namePattern.test(customer_name)) {
            hideLoadingState();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Name Format',
                text: "Full name should contain only letters, spaces, hyphens or apostrophes, and be between 2-100 characters!",
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
            var formData = {
                customer_name: customer_name,
                customer_email: customer_email,
                customer_pass: customer_pass,
                customer_country: customer_country,
                customer_city: customer_city,
                customer_contact: customer_contact,
                user_role: $('input[name="user_role"]').val()
            };
            
            // Add subscription tier if present (for planners)
            var subscription_tier = $('input[name="subscription_tier"]').val();
            if (subscription_tier) {
                formData.subscription_tier = subscription_tier;
            }
            
            // Add payment reference if present (for planners)
            var payment_reference = $('input[name="payment_reference"]').val();
            if (payment_reference) {
                formData.payment_reference = payment_reference;
            }
            
            $.ajax({
                url: '../actions/register_customer_action.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    hideLoadingState();
                    if (response.status === 'success') {
                        // Get the email from the form
                        const email = $('#customer_email').val();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: response.message,
                            showConfirmButton: true,
                            confirmButtonText: 'Go to Login'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to login page with email parameter
                                window.location.href = 'login.php?email=' + encodeURIComponent(email);
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