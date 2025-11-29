<?php
/**
 * Book Consultation Page
 * Allows customers to book consultations with planners
 */

require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['customer_id']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1)) {
    header("Location: ../login/login.php");
    exit;
}

// Get planner ID from URL
if (!isset($_GET['planner_id'])) {
    header("Location: all_products.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Customer';
$planner_id = $_GET['planner_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Consultation - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: var(--cream);
        }
        
        .booking-header {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            padding: 40px 0;
            color: white;
        }
        
        .booking-container {
            max-width: 900px;
            margin: -30px auto 60px;
            padding: 0 20px;
        }
        
        .booking-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 32px;
            margin-bottom: 24px;
        }
        
        .planner-info {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 24px;
            border-bottom: 2px solid var(--gray-light);
            margin-bottom: 24px;
        }
        
        .planner-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--emerald-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--emerald-dark);
            font-weight: 700;
        }
        
        .planner-details h3 {
            margin: 0 0 8px 0;
            color: var(--gray-dark);
        }
        
        .planner-details p {
            margin: 0;
            color: var(--gray-medium);
        }
        
        .form-section {
            margin-bottom: 28px;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .service-option {
            padding: 16px;
            border: 2px solid var(--gray-light);
            border-radius: 12px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .service-option:hover {
            border-color: var(--emerald);
            background: var(--emerald-light);
        }
        
        .service-option.selected {
            border-color: var(--emerald);
            background: var(--emerald-light);
        }
        
        .service-option input[type="radio"] {
            margin-right: 12px;
        }
        
        .service-name {
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 4px;
        }
        
        .service-desc {
            font-size: 0.9rem;
            color: var(--gray-medium);
            margin: 0;
        }
        
        .date-time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .booking-summary {
            background: var(--gray-light);
            padding: 20px;
            border-radius: 12px;
            margin-top: 24px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--cream);
        }
        
        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--emerald-dark);
            padding-top: 16px;
            margin-top: 8px;
            border-top: 2px solid var(--emerald);
        }
        
        .btn-book {
            background: var(--emerald);
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-book:hover {
            background: var(--emerald-dark);
            transform: translateY(-2px);
        }
        
        /* Calendar Styles */
        #calendarGrid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        
        .calendar-day-header {
            text-align: center;
            font-weight: 700;
            color: var(--gray-dark);
            padding: 12px;
            font-size: 0.85rem;
        }
        
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        
        .calendar-day.available {
            background: var(--emerald-light);
            color: var(--emerald-dark);
            border-color: var(--emerald);
        }
        
        .calendar-day.available:hover {
            background: var(--emerald);
            color: white;
            transform: scale(1.05);
        }
        
        .calendar-day.unavailable {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .calendar-day.selected {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald-dark);
            transform: scale(1.05);
        }
        
        .calendar-day.empty {
            background: transparent;
            cursor: default;
        }
        
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        
        .time-slot {
            padding: 12px;
            border: 2px solid var(--gray-light);
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }
        
        .time-slot:hover {
            border-color: var(--emerald);
            background: var(--emerald-light);
        }
        
        .time-slot.selected {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald-dark);
        }
        
        .time-slot.booked {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .calendar-legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 16px;
            font-size: 0.85rem;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        .btn-book:disabled {
            background: var(--gray-medium);
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="booking-header">
        <div class="container text-center">
            <h1 style="margin-bottom: 8px;">Book a Consultation</h1>
            <p style="margin: 0; opacity: 0.9;">Schedule a meeting with your event planner</p>
        </div>
    </div>

    <!-- Booking Container -->
    <div class="booking-container">
        <div class="booking-card">
            <!-- Planner Info -->
            <div class="planner-info" id="plannerInfo">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>

            <!-- Booking Form -->
            <form id="bookingForm">
                <input type="hidden" id="plannerId" name="planner_id" value="<?php echo htmlspecialchars($planner_id); ?>">
                
                <!-- Service Selection -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="bi bi-briefcase"></i>
                        Select Service
                    </h4>
                    <div id="servicesContainer">
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="bi bi-calendar3"></i>
                        Choose Date & Time
                    </h4>
                    
                    <div id="availabilityLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Loading planner's availability...</p>
                    </div>
                    
                    <div id="availabilityCalendar" style="display: none;">
                        <!-- Calendar will be generated by JavaScript -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="prevMonth">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </button>
                                <h5 class="mb-0" id="currentMonth"></h5>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="nextMonth">
                                    Next <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div id="calendarGrid"></div>
                        </div>
                        
                        <div id="timeSlotSection" style="display: none;">
                            <label class="form-label fw-bold">Available Time Slots</label>
                            <div id="timeSlotsContainer" class="time-slots-grid"></div>
                        </div>
                        
                        <input type="hidden" id="consultationDate" name="consultation_date" required>
                        <input type="hidden" id="consultationTime" name="consultation_time" required>
                    </div>
                    
                    <div id="noAvailability" style="display: none;" class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        This planner hasn't set their availability yet. Please try again later.
                    </div>
                </div>

                <!-- Duration -->
                <div class="form-section">
                    <label for="duration" class="form-label">Duration (minutes)</label>
                    <select class="form-select" id="duration" name="duration" required>
                        <option value="30">30 minutes - GHS 50</option>
                        <option value="60" selected>60 minutes - GHS 100</option>
                        <option value="90">90 minutes - GHS 150</option>
                        <option value="120">2 hours - GHS 200</option>
                    </select>
                </div>

                <!-- Location -->
                <div class="form-section">
                    <label for="location" class="form-label">Meeting Location (Optional)</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter meeting location or leave blank for virtual">
                </div>

                <!-- Notes -->
                <div class="form-section">
                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any special requests or information for the planner..."></textarea>
                </div>

                <!-- Summary -->
                <div class="booking-summary">
                    <div class="summary-row">
                        <span>Service:</span>
                        <span id="summaryService">-</span>
                    </div>
                    <div class="summary-row">
                        <span>Duration:</span>
                        <span id="summaryDuration">60 minutes</span>
                    </div>
                    <div class="summary-row">
                        <span>Consultation Fee:</span>
                        <span id="summaryFee">GHS 100.00</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-book" id="bookBtn">
                    <i class="bi bi-calendar-check me-2"></i>Book & Pay Now
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="all_products.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const plannerId = <?php echo json_encode($planner_id); ?>;
        const customerId = <?php echo json_encode($customer_id); ?>;
        const customerEmail = <?php echo json_encode($_SESSION['customer_email'] ?? ''); ?>;
        const orderId = <?php echo json_encode($_GET['order_id'] ?? null); ?>;
        const paystackPublicKey = <?php echo json_encode(PAYSTACK_PUBLIC_KEY); ?>;
        
        let selectedService = null;
        let consultationFee = 100;
        let availableSlots = [];
        let bookedSlots = [];
        let currentMonth = new Date();
        let selectedDate = null;
        let selectedTime = null;

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPlannerInfo();
            loadServices();
            loadPlannerAvailability();
        });

        /**
         * Load planner availability and render calendar
         */
        function loadPlannerAvailability() {
            fetch('../actions/get_planner_slots_action.php?planner_id=' + plannerId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.slots && data.slots.length > 0) {
                        availableSlots = data.slots;
                        document.getElementById('availabilityLoading').style.display = 'none';
                        document.getElementById('availabilityCalendar').style.display = 'block';
                        renderCalendar();
                        setupCalendarNavigation();
                    } else {
                        document.getElementById('availabilityLoading').style.display = 'none';
                        document.getElementById('noAvailability').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading availability:', error);
                    document.getElementById('availabilityLoading').style.display = 'none';
                    document.getElementById('noAvailability').style.display = 'block';
                });
        }

        /**
         * Render calendar for current month
         */
        function renderCalendar() {
            const year = currentMonth.getFullYear();
            const month = currentMonth.getMonth();
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                               'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            let html = '';
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                html += `<div class="calendar-day-header">${day}</div>`;
            });
            
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="calendar-day empty"></div>';
            }
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayOfWeek = date.getDay();
                const dateStr = formatDate(date);
                const hasAvailability = availableSlots.some(slot => parseInt(slot.day_of_week) === dayOfWeek);
                const isPast = date < today;
                
                let classes = 'calendar-day';
                if (isPast) {
                    classes += ' unavailable';
                } else if (hasAvailability) {
                    classes += ' available';
                } else {
                    classes += ' unavailable';
                }
                if (selectedDate === dateStr) classes += ' selected';
                
                const onclick = (!isPast && hasAvailability) ? `onclick="selectDate('${dateStr}')"` : '';
                html += `<div class="${classes}" ${onclick}>${day}</div>`;
            }
            
            document.getElementById('calendarGrid').innerHTML = html;
        }

        function setupCalendarNavigation() {
            document.getElementById('prevMonth').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                renderCalendar();
            });
            document.getElementById('nextMonth').addEventListener('click', () => {
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                renderCalendar();
            });
        }

        function selectDate(dateStr) {
            selectedDate = dateStr;
            selectedTime = null;
            document.getElementById('consultationDate').value = dateStr;
            renderCalendar();
            
            const date = new Date(dateStr);
            const dayOfWeek = date.getDay();
            const daySlots = availableSlots.filter(slot => parseInt(slot.day_of_week) === dayOfWeek);
            displayTimeSlots(daySlots);
        }

        function displayTimeSlots(slots) {
            const container = document.getElementById('timeSlotsContainer');
            const section = document.getElementById('timeSlotSection');
            
            if (slots.length === 0) {
                container.innerHTML = '<p class="text-muted">No time slots available.</p>';
                section.style.display = 'block';
                return;
            }
            
            let html = '';
            slots.forEach(slot => {
                const startMinutes = timeToMinutes(slot.start_time);
                const endMinutes = timeToMinutes(slot.end_time);
                
                for (let minutes = startMinutes; minutes < endMinutes; minutes += 30) {
                    const timeStr = minutesToTime(minutes);
                    const isSelected = selectedTime === timeStr;
                    const classes = isSelected ? 'time-slot selected' : 'time-slot';
                    html += `<div class="${classes}" onclick="selectTime('${timeStr}')">${formatTime(timeStr)}</div>`;
                }
            });
            
            container.innerHTML = html;
            section.style.display = 'block';
        }

        function selectTime(timeStr) {
            selectedTime = timeStr;
            document.getElementById('consultationTime').value = timeStr;
            const date = new Date(selectedDate);
            const dayOfWeek = date.getDay();
            const daySlots = availableSlots.filter(slot => parseInt(slot.day_of_week) === dayOfWeek);
            displayTimeSlots(daySlots);
        }

        function formatDate(date) {
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        }

        function timeToMinutes(timeStr) {
            const parts = timeStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        function minutesToTime(minutes) {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
        }

        function formatTime(timeStr) {
            const parts = timeStr.split(':');
            let hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${minutes} ${ampm}`;
        }

        /**
         * Load planner information
         */
        function loadPlannerInfo() {
            fetch('../actions/get_planner_info_action.php?planner_id=' + plannerId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayPlannerInfo(data.planner);
                    }
                })
                .catch(error => {
                    console.error('Error loading planner info:', error);
                });
        }

        /**
         * Display planner information
         */
        function displayPlannerInfo(planner) {
            const initials = planner.customer_name.split(' ').map(n => n[0]).join('').toUpperCase();
            
            document.getElementById('plannerInfo').innerHTML = `
                <div class="planner-avatar">${initials}</div>
                <div class="planner-details">
                    <h3>${escapeHtml(planner.customer_name)}</h3>
                    <p><i class="bi bi-envelope me-2"></i>${escapeHtml(planner.customer_email)}</p>
                    ${planner.customer_contact ? `<p><i class="bi bi-phone me-2"></i>${escapeHtml(planner.customer_contact)}</p>` : ''}
                </div>
            `;
        }

        /**
         * Load available services
         */
        function loadServices() {
            fetch('../actions/get_service_types_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayServices(data.services);
                    }
                })
                .catch(error => {
                    console.error('Error loading services:', error);
                });
        }

        /**
         * Display services
         */
        function displayServices(services) {
            const container = document.getElementById('servicesContainer');
            
            let html = '';
            services.forEach(service => {
                html += `
                    <label class="service-option" onclick="selectService(${service.service_id}, '${escapeHtml(service.service_name)}')">
                        <input type="radio" name="service" value="${service.service_id}" required>
                        <div>
                            <div class="service-name">${escapeHtml(service.service_name)}</div>
                            <p class="service-desc">${escapeHtml(service.service_description || '')}</p>
                        </div>
                    </label>
                `;
            });
            
            container.innerHTML = html;
        }

        /**
         * Select service
         */
        function selectService(serviceId, serviceName) {
            selectedService = {id: serviceId, name: serviceName};
            document.getElementById('summaryService').textContent = serviceName;
            
            // Update selected styling
            document.querySelectorAll('.service-option').forEach(opt => opt.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
        }

        /**
         * Update duration and fee
         */
        document.getElementById('duration').addEventListener('change', function() {
            const duration = parseInt(this.value);
            const feeMap = {30: 50, 60: 100, 90: 150, 120: 200};
            consultationFee = feeMap[duration] || 100;
            
            document.getElementById('summaryDuration').textContent = duration + ' minutes';
            document.getElementById('summaryFee').textContent = 'GHS ' + consultationFee.toFixed(2);
        });

        /**
         * Handle form submission
         */
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedService) {
                alert('Please select a service');
                return;
            }
            
            // Validate date and time selected
            if (!selectedDate || !selectedTime) {
                alert('Please select a date and time for your consultation');
                return;
            }
            
            const formData = {
                planner_id: plannerId,
                service_id: selectedService.id,
                consultation_date: selectedDate,
                consultation_time: selectedTime,
                duration: document.getElementById('duration').value,
                fee: consultationFee,
                location: document.getElementById('location').value,
                notes: document.getElementById('notes').value
            };
            
            // Add order_id if booking from order history
            if (orderId) {
                formData.order_id = orderId;
            }
            
            // Create consultation booking
            document.getElementById('bookBtn').disabled = true;
            document.getElementById('bookBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            fetch('../actions/create_consultation_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Initialize Paystack payment
                    initiatePayment(data.consultation_id, formData.fee);
                } else {
                    alert(data.message || 'Failed to create booking');
                    document.getElementById('bookBtn').disabled = false;
                    document.getElementById('bookBtn').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Book & Pay Now';
                }
            })
            .catch(error => {
                console.error('Error creating consultation:', error);
                alert('Failed to create booking. Please try again.');
                document.getElementById('bookBtn').disabled = false;
                document.getElementById('bookBtn').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Book & Pay Now';
            });
        });

        /**
         * Initialize Paystack payment
         */
        function initiatePayment(consultationId, amount) {
            // Validate required data
            if (!customerEmail || customerEmail === '') {
                alert('Error: Customer email not found. Please log in again.');
                location.href = '../login/login.php';
                return;
            }
            
            if (!amount || amount <= 0) {
                alert('Error: Invalid consultation fee');
                return;
            }
            
            const reference = 'CONSULT-' + consultationId + '-' + Date.now();
            
            console.log('Paystack Payment Data:', {
                key: paystackPublicKey,
                email: customerEmail,
                amount: amount * 100,
                currency: 'GHS',
                ref: reference,
                consultation_id: consultationId
            });
            
            const handler = PaystackPop.setup({
                key: paystackPublicKey,
                email: customerEmail,
                amount: amount * 100, // Convert to pesewas
                currency: 'GHS',
                ref: reference,
                metadata: {
                    consultation_id: consultationId,
                    customer_id: customerId,
                    planner_id: plannerId
                },
                callback: function(response) {
                    // Verify payment
                    verifyPayment(response.reference, consultationId);
                },
                onClose: function() {
                    document.getElementById('bookBtn').disabled = false;
                    document.getElementById('bookBtn').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Book & Pay Now';
                }
            });
            
            handler.openIframe();
        }

        /**
         * Verify payment
         */
        function verifyPayment(reference, consultationId) {
            fetch('../actions/verify_consultation_payment_action.php?reference=' + reference + '&consultation_id=' + consultationId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'consultation_success.php?consultation_id=' + consultationId;
                    } else {
                        alert('Payment verification failed. Please contact support.');
                        document.getElementById('bookBtn').disabled = false;
                        document.getElementById('bookBtn').innerHTML = '<i class="bi bi-calendar-check me-2"></i>Book & Pay Now';
                    }
                })
                .catch(error => {
                    console.error('Error verifying payment:', error);
                    alert('Payment verification failed. Please contact support.');
                });
        }

        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
