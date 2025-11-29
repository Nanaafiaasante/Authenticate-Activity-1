<?php
/**
 * Consultation Dashboard - Planner Analytics
 * Shows bookings, revenue, and consultation management
 */

require_once '../settings/core.php';

// Check if user is logged in and is a planner
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login/login.php");
    exit;
}

$planner_id = $_SESSION['customer_id'];
$planner_name = $_SESSION['customer_name'] ?? 'Planner';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Dashboard - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafb 0%, #f1f5f9 100%);
            font-family: 'Inter', sans-serif;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(234, 224, 218, 0.6);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(139, 118, 108, 0.08);
            margin-bottom: 24px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(139, 118, 108, 0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 8px;
            font-family: 'Playfair Display', serif;
        }
        
        .stat-label {
            font-size: 0.95rem;
            color: #5a5a5a;
            font-weight: 500;
        }
        
        .section-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(234, 224, 218, 0.6);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 20px rgba(139, 118, 108, 0.08);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title i {
            color: #C9A961;
        }
        
        .consultation-item {
            padding: 16px;
            border-bottom: 1px solid rgba(234, 224, 218, 0.5);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
        }
        
        .consultation-item:hover {
            background: rgba(249, 245, 240, 0.5);
        }
        
        .consultation-item:last-child {
            border-bottom: none;
        }
        
        .consultation-info h6 {
            margin: 0 0 8px 0;
            color: #2c2c2c;
            font-weight: 600;
        }
        
        .consultation-meta {
            font-size: 0.9rem;
            color: #5a5a5a;
        }
        
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fed7aa;
            color: #92400e;
        }
        
        .status-completed {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary-action {
            background: linear-gradient(135deg, #1e4d2b, #2d5a3a);
            color: white;
        }
        
        .btn-primary-action:hover {
            background: linear-gradient(135deg, #0f261a, #1e4d2b);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 77, 43, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #8b766c;
        }
        
        .empty-icon {
            font-size: 3rem;
            margin-bottom: 16px;
            color: #C9A961;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .btn-group .btn {
            border: 1px solid rgba(201, 169, 97, 0.3);
        }
        
        .btn-group .btn.active {
            background: linear-gradient(135deg, #C9A961, #D4AF37);
            color: white;
            border-color: #C9A961;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #1e4d2b, #2d5a3a) !important;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="header-section">
        <div class="container-fluid">
            <div class="header-container">
                <!-- Left: Logo -->
                <div class="header-left">
                    <a href="../index.php" class="vc-logo">
                        <div class="vc-logo-ring"></div>
                        <div class="vc-logo-text">
                            <div class="vc-logo-main">VendorConnect</div>
                            <div class="vc-logo-sub">GHANA</div>
                        </div>
                    </a>
                </div>
                
                <!-- Center: Page Title -->
                <div class="header-center">
                    <h1 class="page-title">Consultation Dashboard</h1>
                    <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($planner_name); ?>!</p>
                </div>
                
                <!-- Right: Navigation -->
                <div class="header-right">
                    <a href="dashboard.php" class="header-nav-btn">
                        <i class="bi bi-grid"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <a href="availability.php" class="header-nav-btn">
                        <i class="bi bi-clock"></i>
                        <span class="nav-label">Availability</span>
                    </a>
                    <a href="../login/logout.php" class="header-nav-btn logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid" style="max-width: 1400px;">
            <!-- Statistics Cards -->
            <div class="row" id="statsContainer">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-value" id="totalBookings">-</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <div class="stat-value" id="totalRevenue">-</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fed7aa; color: #92400e;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-value" id="upcomingBookings">-</div>
                    <div class="stat-label">Upcoming</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e9d5ff; color: #6b21a8;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-value" id="completedConsultations">-</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Consultations -->
            <div class="col-lg-6">
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="bi bi-calendar-event"></i>
                        Upcoming Consultations
                    </h3>
                    <div id="upcomingConsultations">
                        <div class="empty-state">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Services -->
            <div class="col-lg-6">
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="bi bi-graph-up"></i>
                        Service Breakdown
                    </h3>
                    <div id="serviceBreakdown">
                        <div class="empty-state">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Consultations -->
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="section-title mb-0">
                    <i class="bi bi-list-check"></i>
                    All Consultations
                </h3>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" onclick="filterConsultations('all')">All</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterConsultations('pending')">Pending</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterConsultations('confirmed')">Confirmed</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterConsultations('completed')">Completed</button>
                </div>
            </div>
            <div id="allConsultations">
                <div class="empty-state">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">Loading...</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Consultation Details Modal -->
    <div class="modal fade" id="consultationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Consultation Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="consultationDetails">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let allConsultationsData = [];
        
        // Load all data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalytics();
            loadUpcomingConsultations();
            loadAllConsultations();
        });

        /**
         * Load planner analytics
         */
        function loadAnalytics() {
            fetch('../actions/get_planner_analytics_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayAnalytics(data.analytics);
                    }
                })
                .catch(error => {
                    console.error('Error loading analytics:', error);
                });
        }

        /**
         * Display analytics
         */
        function displayAnalytics(analytics) {
            document.getElementById('totalBookings').textContent = analytics.total_bookings || 0;
            document.getElementById('totalRevenue').textContent = 'GHS ' + parseFloat(analytics.total_revenue || 0).toFixed(2);
            document.getElementById('upcomingBookings').textContent = analytics.upcoming_bookings || 0;
            document.getElementById('completedConsultations').textContent = analytics.completed_consultations || 0;
            
            // Display service breakdown
            displayServiceBreakdown(analytics.service_breakdown || []);
        }

        /**
         * Display service breakdown
         */
        function displayServiceBreakdown(services) {
            const container = document.getElementById('serviceBreakdown');
            
            if (!services || services.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No services data yet</p></div>';
                return;
            }
            
            let html = '';
            services.forEach(service => {
                const percentage = Math.min(100, (service.count / services[0].count) * 100);
                html += `
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-weight: 600; color: var(--gray-dark);">${escapeHtml(service.service_name || 'N/A')}</span>
                            <span style="color: var(--emerald); font-weight: 600;">GHS ${parseFloat(service.revenue || 0).toFixed(2)}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: ${percentage}%; background: var(--emerald);"></div>
                        </div>
                        <small class="text-muted">${service.count} booking(s)</small>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        /**
         * Load upcoming consultations
         */
        function loadUpcomingConsultations() {
            fetch('../actions/get_upcoming_consultations_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayUpcomingConsultations(data.consultations);
                    } else {
                        document.getElementById('upcomingConsultations').innerHTML = 
                            '<div class="empty-state"><div class="empty-icon"><i class="bi bi-calendar-x"></i></div><p>No upcoming consultations</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading consultations:', error);
                });
        }

        /**
         * Display upcoming consultations
         */
        function displayUpcomingConsultations(consultations) {
            const container = document.getElementById('upcomingConsultations');
            
            if (!consultations || consultations.length === 0) {
                container.innerHTML = '<div class="empty-state"><div class="empty-icon"><i class="bi bi-calendar-x"></i></div><p>No upcoming consultations</p></div>';
                return;
            }
            
            let html = '';
            consultations.forEach(consultation => {
                html += createConsultationItem(consultation, true);
            });
            
            container.innerHTML = html;
        }

        /**
         * Load all consultations
         */
        function loadAllConsultations(status = null) {
            const url = status ? `../actions/get_planner_consultations_action.php?status=${status}` : '../actions/get_planner_consultations_action.php';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        allConsultationsData = data.consultations;
                        displayAllConsultations(data.consultations);
                    } else {
                        document.getElementById('allConsultations').innerHTML = 
                            '<div class="empty-state"><p>No consultations found</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading consultations:', error);
                });
        }

        /**
         * Display all consultations
         */
        function displayAllConsultations(consultations) {
            const container = document.getElementById('allConsultations');
            
            if (!consultations || consultations.length === 0) {
                container.innerHTML = '<div class="empty-state"><p>No consultations found</p></div>';
                return;
            }
            
            let html = '';
            consultations.forEach(consultation => {
                html += createConsultationItem(consultation, false);
            });
            
            container.innerHTML = html;
        }

        /**
         * Create consultation item HTML
         */
        function createConsultationItem(consultation, isUpcoming) {
            const statusClass = getStatusClass(consultation.booking_status);
            const date = new Date(consultation.consultation_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const time = formatTime(consultation.consultation_time);
            const isPending = consultation.booking_status === 'pending';
            const isConfirmed = consultation.booking_status === 'confirmed';
            
            let actionButtons = '';
            if (isPending) {
                actionButtons = `
                    <button class="btn btn-sm btn-success" onclick="confirmConsultation(${consultation.consultation_id})" title="Confirm Booking">
                        <i class="bi bi-check-circle me-1"></i>Confirm
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="cancelConsultation(${consultation.consultation_id})" title="Cancel Booking">
                        <i class="bi bi-x-circle"></i>
                    </button>
                `;
            } else if (isConfirmed) {
                actionButtons = `
                    <button class="btn btn-sm btn-primary" onclick="completeConsultation(${consultation.consultation_id})" title="Mark as Completed">
                        <i class="bi bi-check-all"></i>
                    </button>
                `;
            }
            
            return `
                <div class="consultation-item">
                    <div class="consultation-info">
                        <h6>${escapeHtml(consultation.customer_name || 'Customer')}</h6>
                        <div class="consultation-meta">
                            <i class="bi bi-calendar3 me-1"></i>${date} at ${time} 
                            <span class="mx-2">•</span>
                            <i class="bi bi-briefcase me-1"></i>${escapeHtml(consultation.service_name || 'Consultation')}
                            <span class="mx-2">•</span>
                            <strong>GHS ${parseFloat(consultation.consultation_fee || 0).toFixed(2)}</strong>
                            ${consultation.customer_notes ? `<br><small class="text-muted"><i class="bi bi-chat-text me-1"></i>${escapeHtml(consultation.customer_notes)}</small>` : ''}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-badge ${statusClass}">${consultation.booking_status}</span>
                        ${actionButtons}
                        <button class="btn btn-sm btn-outline-primary" onclick="viewConsultation(${consultation.consultation_id})"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            `;
        }

        /**
         * Filter consultations
         */
        function filterConsultations(status) {
            // Update active button
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Load filtered consultations
            if (status === 'all') {
                loadAllConsultations();
            } else {
                loadAllConsultations(status);
            }
        }

        /**
         * View consultation details
         */
        function viewConsultation(consultationId) {
            fetch('../actions/get_consultation_details_action.php?consultation_id=' + consultationId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayConsultationDetails(data.consultation);
                        new bootstrap.Modal(document.getElementById('consultationModal')).show();
                    }
                })
                .catch(error => {
                    console.error('Error loading consultation details:', error);
                });
        }

        /**
         * Display consultation details
         */
        function displayConsultationDetails(consultation) {
            const date = new Date(consultation.consultation_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            const time = formatTime(consultation.consultation_time);
            const statusClass = getStatusClass(consultation.booking_status);
            
            document.getElementById('consultationDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Customer</label>
                        <p>${escapeHtml(consultation.customer_name)}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p>${escapeHtml(consultation.customer_email)}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Contact</label>
                        <p>${escapeHtml(consultation.customer_contact || 'N/A')}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Service</label>
                        <p>${escapeHtml(consultation.service_name)}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date & Time</label>
                        <p>${date} at ${time}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Duration</label>
                        <p>${consultation.duration_minutes} minutes</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Fee</label>
                        <p>GHS ${parseFloat(consultation.consultation_fee).toFixed(2)}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <p><span class="status-badge ${statusClass}">${consultation.booking_status}</span></p>
                    </div>
                    ${consultation.customer_notes ? `
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Customer Notes</label>
                        <p>${escapeHtml(consultation.customer_notes)}</p>
                    </div>
                    ` : ''}
                    ${consultation.meeting_location ? `
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Meeting Location</label>
                        <p>${escapeHtml(consultation.meeting_location)}</p>
                    </div>
                    ` : ''}
                </div>
            `;
        }

        /**
         * Get status class
         */
        function getStatusClass(status) {
            const statusMap = {
                'confirmed': 'status-confirmed',
                'pending': 'status-pending',
                'completed': 'status-completed',
                'cancelled': 'status-cancelled'
            };
            return statusMap[status.toLowerCase()] || 'status-pending';
        }

        /**
         * Format time
         */
        function formatTime(timeString) {
            const parts = timeString.split(':');
            const hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }

        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        /**
         * Confirm consultation (first-come, first-served)
         */
        function confirmConsultation(consultationId) {
            if (!confirm('Confirm this consultation booking?')) {
                return;
            }
            
            updateConsultationStatus(consultationId, 'confirmed', '');
        }

        /**
         * Complete consultation
         */
        function completeConsultation(consultationId) {
            const notes = prompt('Add any final notes (optional):');
            if (notes === null) return; // User cancelled
            
            updateConsultationStatus(consultationId, 'completed', notes);
        }

        /**
         * Cancel consultation
         */
        function cancelConsultation(consultationId) {
            const reason = prompt('Reason for cancellation:');
            if (!reason) {
                alert('Please provide a cancellation reason');
                return;
            }
            
            updateConsultationStatus(consultationId, 'cancelled', reason);
        }

        /**
         * Update consultation status
         */
        function updateConsultationStatus(consultationId, status, notes) {
            fetch('../actions/update_consultation_status_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    consultation_id: consultationId,
                    status: status,
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    // Reload consultations
                    loadAnalytics();
                    loadUpcomingConsultations();
                    loadAllConsultations();
                } else {
                    alert(data.message || 'Failed to update consultation status');
                }
            })
            .catch(error => {
                console.error('Error updating consultation:', error);
                alert('Failed to update consultation. Please try again.');
            });
        }
    </script>
</body>
</html>
