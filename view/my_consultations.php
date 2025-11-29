<?php
/**
 * My Consultations Page
 * Shows customer's consultation bookings with status
 */

require_once '../settings/core.php';

// Check if user is logged in and is a customer
if (!isset($_SESSION['customer_id']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1)) {
    header("Location: ../login/login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Consultations - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/all_products.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: var(--cream);
            font-family: 'Inter', sans-serif;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .consultation-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--emerald);
        }
        
        .consultation-card.pending {
            border-left-color: #ffc107;
        }
        
        .consultation-card.confirmed {
            border-left-color: #198754;
        }
        
        .consultation-card.completed {
            border-left-color: #6c757d;
        }
        
        .consultation-card.cancelled {
            border-left-color: #dc3545;
            opacity: 0.7;
        }
        
        .status-badge {
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-badge.confirmed {
            background: #d1e7dd;
            color: #0f5132;
        }
        
        .status-badge.completed {
            background: #d3d3d4;
            color: #495057;
        }
        
        .status-badge.cancelled {
            background: #f8d7da;
            color: #842029;
        }
        
        .consultation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        
        .consultation-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-item i {
            color: var(--emerald);
            font-size: 1.1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
    </style>
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
            
            <!-- Center - Title -->
            <div class="header-center">
                <h1 class="page-title"><i class="bi bi-calendar-check me-2"></i>My Consultations</h1>
            </div>
            
            <!-- Navigation -->
            <div class="header-right">
                <a href="all_products.php" class="btn-header-nav">
                    <i class="bi bi-shop"></i>
                    <span class="btn-nav-label">Store</span>
                </a>
                <a href="cart.php" class="btn-header-nav">
                    <i class="bi bi-cart3"></i>
                    <span class="btn-nav-label">Cart</span>
                </a>
                <a href="orders.php" class="btn-header-nav">
                    <i class="bi bi-bag-check"></i>
                    <span class="btn-nav-label">Orders</span>
                </a>
                <a href="../login/logout.php" class="btn-header-nav btn-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="btn-nav-label">Logout</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div id="consultationsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading consultations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const customerId = <?php echo json_encode($customer_id); ?>;

        /**
         * Load customer's consultations
         */
        function loadConsultations() {
            fetch('../actions/get_customer_consultations_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.consultations && data.consultations.length > 0) {
                        displayConsultations(data.consultations);
                    } else {
                        displayEmptyState();
                    }
                })
                .catch(error => {
                    console.error('Error loading consultations:', error);
                    document.getElementById('consultationsContainer').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load consultations. Please try again.
                        </div>
                    `;
                });
        }

        /**
         * Display consultations
         */
        function displayConsultations(consultations) {
            const container = document.getElementById('consultationsContainer');
            
            let html = '';
            consultations.forEach(consultation => {
                const statusClass = consultation.booking_status.toLowerCase();
                const statusText = consultation.booking_status.charAt(0).toUpperCase() + consultation.booking_status.slice(1);
                const paymentStatus = consultation.payment_status === 'paid' ? 'Paid' : 'Unpaid';
                const paymentColor = consultation.payment_status === 'paid' ? 'success' : 'warning';
                
                html += `
                    <div class="consultation-card ${statusClass}">
                        <div class="consultation-header">
                            <div>
                                <h5 class="mb-1">${consultation.service_name}</h5>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-person me-1"></i>
                                    Planner: ${consultation.planner_name}
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="status-badge ${statusClass}">${statusText}</span>
                                <br>
                                <span class="badge bg-${paymentColor} mt-2">${paymentStatus}</span>
                            </div>
                        </div>
                        
                        <div class="consultation-info">
                            <div class="info-item">
                                <i class="bi bi-calendar3"></i>
                                <div>
                                    <small class="text-muted d-block">Date</small>
                                    <strong>${formatDate(consultation.consultation_date)}</strong>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <small class="text-muted d-block">Time</small>
                                    <strong>${formatTime(consultation.consultation_time)}</strong>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="bi bi-hourglass-split"></i>
                                <div>
                                    <small class="text-muted d-block">Duration</small>
                                    <strong>${consultation.duration_minutes} minutes</strong>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="bi bi-cash"></i>
                                <div>
                                    <small class="text-muted d-block">Fee</small>
                                    <strong>GHS ${parseFloat(consultation.consultation_fee).toFixed(2)}</strong>
                                </div>
                            </div>
                        </div>
                        
                        ${consultation.meeting_location ? `
                        <div class="mt-3">
                            <i class="bi bi-geo-alt text-muted me-1"></i>
                            <small class="text-muted">Location:</small> ${consultation.meeting_location}
                        </div>
                        ` : ''}
                        
                        ${consultation.customer_notes ? `
                        <div class="mt-2">
                            <i class="bi bi-chat-left-text text-muted me-1"></i>
                            <small class="text-muted">Notes:</small> ${consultation.customer_notes}
                        </div>
                        ` : ''}
                        
                        ${consultation.planner_notes ? `
                        <div class="mt-2 p-2 bg-light rounded">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <small class="text-muted">Planner's Note:</small> ${consultation.planner_notes}
                        </div>
                        ` : ''}
                        
                        <div class="mt-3 text-muted small">
                            <i class="bi bi-calendar-plus me-1"></i>
                            Booked on: ${formatDateTime(consultation.created_at)}
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        /**
         * Display empty state
         */
        function displayEmptyState() {
            document.getElementById('consultationsContainer').innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <h4 class="text-muted">No Consultations Yet</h4>
                    <p class="text-muted">You haven't booked any consultations yet.</p>
                    <a href="all_products.php" class="btn btn-primary">
                        <i class="bi bi-shop me-2"></i>Browse Services
                    </a>
                </div>
            `;
        }

        /**
         * Format date
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                weekday: 'short', 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }

        /**
         * Format time
         */
        function formatTime(timeString) {
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }

        /**
         * Format date and time
         */
        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Load consultations on page load
        document.addEventListener('DOMContentLoaded', loadConsultations);
    </script>
</body>
</html>
