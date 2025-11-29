<?php
/**
 * Order History Page
 * Shows customer's order history with details
 */

require_once '../settings/core.php';

// Check if user is logged in
if (!check_login()) {
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
    <title>Order History - VendorConnect Ghana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fafb 0%, #f1f5f9 100%);
            position: relative;
            min-height: 100vh;
        }

        /* BOTANICAL DECORATIONS */
        .botanical-tl, .botanical-tr, .botanical-bl, .botanical-br {
            position: fixed;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(30, 77, 43, 0.15), transparent 70%);
            pointer-events: none;
            z-index: 1;
        }
        .botanical-tl { top: 0; left: 0; }
        .botanical-tr { top: 0; right: 0; }
        .botanical-bl { bottom: 0; left: 0; }
        .botanical-br { bottom: 0; right: 0; }

        /* GOLD FRAMES */
        .gold-frame-tr, .gold-frame-bl {
            position: fixed;
            width: 100px;
            height: 100px;
            border: 2px solid rgba(201, 169, 97, 0.3);
            pointer-events: none;
            z-index: 1;
        }
        .gold-frame-tr { top: 30px; right: 30px; }
        .gold-frame-bl { bottom: 30px; left: 30px; }

        /* GOLD DOTS */
        .gold-dot {
            position: fixed;
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #C9A961, #D4AF37);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            box-shadow: 0 0 10px rgba(201, 169, 97, 0.5);
        }
        .dot-tr1 { top: 60px; right: 80px; }
        .dot-tr2 { top: 100px; right: 40px; }
        .dot-tr3 { top: 140px; right: 100px; }
        .dot-tr4 { top: 80px; right: 140px; }
        .dot-tr5 { top: 160px; right: 60px; }
        .dot-tr6 { top: 120px; right: 160px; }
        .dot-tr7 { top: 180px; right: 120px; }
        .dot-bl1 { bottom: 60px; left: 80px; }
        .dot-bl2 { bottom: 100px; left: 40px; }
        .dot-bl3 { bottom: 140px; left: 100px; }
        .dot-bl4 { bottom: 80px; left: 140px; }
        .dot-bl5 { bottom: 160px; left: 60px; }
        .dot-bl6 { bottom: 120px; left: 160px; }
        .dot-bl7 { bottom: 180px; left: 120px; }

        /* LOGO STYLES */
        .vc-logo {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            gap: 12px;
            position: relative;
        }
        .vc-logo-ring {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #D4AF37 0%, #F4E4C1 50%, #C9A961 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 4px 12px rgba(212, 175, 55, 0.4),
                inset 0 2px 8px rgba(255, 255, 255, 0.5);
            position: relative;
        }
        .vc-logo-ring::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background: radial-gradient(circle, #FFF 0%, #F4E4C1 100%);
            border-radius: 50%;
            box-shadow: inset 0 2px 4px rgba(201, 169, 97, 0.3);
        }
        .vc-logo-ring::after {
            content: '💍';
            position: absolute;
            font-size: 16px;
            z-index: 2;
        }
        .vc-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }
        .vc-logo-main {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e4d2b;
            letter-spacing: 0.5px;
        }
        .vc-logo-sub {
            font-family: 'Inter', sans-serif;
            font-size: 0.65rem;
            color: #C9A961;
            letter-spacing: 2px;
            font-weight: 600;
            margin-top: 2px;
        }

        /* HEADER SECTION */
        .header-section {
            position: relative;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 2px solid rgba(201, 169, 97, 0.15);
            padding: 1.25rem 0;
            margin-bottom: 2rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(30, 77, 43, 0.08);
        }
        .header-section .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 2rem;
            flex: 0 0 auto;
        }
        .header-center {
            flex: 1;
            max-width: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 0 0 auto;
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e4d2b;
            margin: 0;
        }
        .btn-header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 25px;
            background: white;
            border: 2px solid #1e4d2b;
            color: #1e4d2b;
            text-decoration: none;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-header-nav:hover {
            background: #1e4d2b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 77, 43, 0.2);
        }
        .btn-header-nav i {
            font-size: 1.1rem;
        }
        .btn-nav-label {
            white-space: nowrap;
        }
        .btn-header-nav.btn-logout {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-header-nav.btn-logout:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }
        
        .orders-container {
            max-width: 1200px;
            margin: 2rem auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }
        
        .order-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: var(--gray-light);
            border-bottom: 2px solid var(--emerald-light);
        }
        
        .order-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .order-info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .order-label {
            font-size: 0.85rem;
            color: var(--gray-medium);
            font-weight: 500;
        }
        
        .order-value {
            font-size: 1rem;
            color: var(--gray-dark);
            font-weight: 600;
        }
        
        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fed7aa;
            color: #92400e;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .order-body {
            padding: 24px;
        }
        
        .order-items {
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-title {
            font-weight: 600;
            color: var(--gray-dark);
            margin-bottom: 4px;
        }
        
        .item-meta {
            font-size: 0.9rem;
            color: var(--gray-medium);
        }
        
        .item-price {
            font-weight: 700;
            color: var(--emerald);
            text-align: right;
        }
        
        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: var(--gray-light);
            border-top: 2px solid var(--emerald-light);
        }
        
        .order-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--emerald-dark);
        }
        
        .order-actions {
            display: flex;
            gap: 12px;
        }
        
        .btn-order {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: var(--emerald);
            color: white;
        }
        
        .btn-view:hover {
            background: var(--emerald-dark);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--gray-medium);
            margin-bottom: 20px;
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
                <h1 class="page-title"><i class="bi bi-bag-check me-2"></i>Order History</h1>
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
                <a href="my_consultations.php" class="btn-header-nav">
                    <i class="bi bi-calendar-check"></i>
                    <span class="btn-nav-label">Consultations</span>
                </a>
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="../login/logout.php" class="btn-header-nav btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="btn-nav-label">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="../login/login.php" class="btn-header-nav">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="btn-nav-label">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Orders Container -->
    <div class="orders-container">

        <div id="ordersContainer">
            <div class="text-center py-5">
                <div class="spinner-border" style="color: #1e4d2b;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Loading your orders...</p>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
        });

        /**
         * Load customer orders
         */
        function loadOrders() {
            fetch('../actions/get_user_orders_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayOrders(data.orders);
                    } else {
                        showEmptyState();
                    }
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    document.getElementById('ordersContainer').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Failed to load orders. Please try again.
                        </div>
                    `;
                });
        }

        /**
         * Display orders
         */
        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            
            if (!orders || orders.length === 0) {
                showEmptyState();
                return;
            }

            let html = '';
            orders.forEach(order => {
                html += createOrderCard(order);
            });

            container.innerHTML = html;
        }

        /**
         * Toggle order details visibility
         */
        function toggleOrderDetails(orderId) {
            const bodyContainer = document.getElementById('orderBody' + orderId);
            const viewBtn = document.getElementById('viewBtn' + orderId);
            
            if (bodyContainer.style.display === 'none' || bodyContainer.style.display === '') {
                // Show details
                bodyContainer.style.display = 'block';
                viewBtn.innerHTML = '<i class="bi bi-eye-slash me-2"></i>Hide Details';
                
                // Load details if not already loaded
                if (!bodyContainer.dataset.loaded) {
                    loadOrderDetails(orderId);
                }
            } else {
                // Hide details
                bodyContainer.style.display = 'none';
                viewBtn.innerHTML = '<i class="bi bi-eye me-2"></i>View Details';
            }
        }

        /**
         * Create order card HTML
         */
        function createOrderCard(order) {
            const statusClass = getStatusClass(order.order_status);
            const orderDate = formatDate(order.order_date);
            
            return `
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <div class="order-info-item">
                                <span class="order-label">Order ID</span>
                                <span class="order-value">#${order.invoice_no}</span>
                            </div>
                            <div class="order-info-item">
                                <span class="order-label">Date</span>
                                <span class="order-value">${orderDate}</span>
                            </div>
                            <div class="order-info-item">
                                <span class="order-label">Items</span>
                                <span class="order-value">${order.item_count} item(s)</span>
                            </div>
                        </div>
                        <span class="order-status ${statusClass}">${order.order_status}</span>
                    </div>
                    <div class="order-body" id="orderBody${order.order_id}" style="display: none;">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm" style="color: #1e4d2b;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="d-block mt-2 text-muted">Loading items...</small>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="order-total">
                            Total: ${order.currency || 'GHS'} ${parseFloat(order.payment_amount || order.total_amount || 0).toFixed(2)}
                        </div>
                        <div class="order-actions">
                            <button class="btn-order btn-view" id="viewBtn${order.order_id}" onclick="toggleOrderDetails(${order.order_id})">
                                <i class="bi bi-eye me-2"></i>View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        /**
         * Load order items into card
         */
        function loadOrderDetails(orderId) {
            const bodyContainer = document.getElementById('orderBody' + orderId);
            
            // Show loading state
            bodyContainer.innerHTML = `
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm" style="color: #1e4d2b;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <small class="d-block mt-2 text-muted">Loading items...</small>
                </div>
            `;
            
            fetch('../actions/get_order_details_action.php?order_id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        displayOrderItems(bodyContainer, data.items, data.consultation_booked, data.consultation_status);
                        bodyContainer.dataset.loaded = 'true';
                    } else {
                        bodyContainer.innerHTML = '<div class="alert alert-danger">Failed to load order details</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading order details:', error);
                    bodyContainer.innerHTML = '<div class="alert alert-danger">Error loading details. Please try again.</div>';
                });
        }

        /**
         * Display order items
         */
        function displayOrderItems(container, items, consultationBooked, consultationStatus) {
            console.log('=== ORDER DETAILS DEBUG ===');
            console.log('Items received:', items);
            console.log('First item selected_items:', items[0]?.selected_items);
            console.log('Consultation info:', {consultationBooked, consultationStatus});
            
            let html = '<div class="order-items">';
            
            // Get planner ID and order info from first item (assuming all items from same vendor)
            const plannerId = items.length > 0 ? items[0].product_cat : null;
            const orderId = items.length > 0 ? items[0].order_id : null;
            const vendorId = items.length > 0 ? items[0].vendor_customer_id : null;
            const vendorName = items.length > 0 ? items[0].vendor_name : null;
            const existingRating = items.length > 0 ? items[0].rating : null;
            const existingReview = items.length > 0 ? items[0].review_comment : null;
            
            // Show vendor information at the top
            if (vendorName) {
                html += `
                    <div style="background: linear-gradient(135deg, #1e4d2b 0%, #2d6b3f 100%); color: white; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <i class="bi bi-shop" style="font-size: 1.2rem;"></i>
                            <strong style="font-size: 0.9rem; opacity: 0.9;">Vendor / Planner</strong>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 600;">${escapeHtml(vendorName)}</div>
                    </div>
                `;
            }
            
            items.forEach(item => {
                const imagePath = item.product_image ? '../' + item.product_image : '../uploads/default-product.jpg';
                html += `
                    <div class="order-item">
                        <img src="${imagePath}" alt="${escapeHtml(item.product_title)}" class="item-image" onerror="this.src='../uploads/default-product.jpg'">
                        <div class="item-details">
                            <div class="item-title">${escapeHtml(item.product_title)}</div>
                            <div class="item-meta">Qty: ${item.qty} × GHS ${parseFloat(item.product_price).toFixed(2)}</div>
                `;
                
                // Show selected package items if they exist
                if (item.selected_items) {
                    try {
                        const selectedItems = JSON.parse(item.selected_items);
                        if (selectedItems && selectedItems.length > 0) {
                            html += `
                                <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-radius: 4px; border-left: 3px solid #C9A961;">
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #1e4d2b; margin-bottom: 4px;">
                                        <i class="bi bi-box-seam" style="color: #C9A961;"></i> Selected Package Items:
                                    </div>
                                    <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem; color: #495057;">
                            `;
                            selectedItems.forEach(pkgItem => {
                                html += `<li>${escapeHtml(pkgItem.item_name)}</li>`;
                            });
                            html += `
                                    </ul>
                                </div>
                            `;
                        }
                    } catch (e) {
                        console.error('Error parsing selected_items:', e);
                    }
                }
                
                html += `
                        </div>
                        <div class="item-price">GHS ${parseFloat(item.item_total).toFixed(2)}</div>
                    </div>
                `;
            });
            
            html += '</div>';
            
            // Add rating section if vendor exists
            if (vendorId && orderId) {
                html += '<div style="padding: 16px; border-top: 2px solid #f0f0f0; margin-top: 12px;">';
                
                if (existingRating) {
                    // Show existing rating
                    html += `
                        <div style="background: #f8f9fa; border-radius: 8px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="bi bi-check-circle-fill" style="color: #198754; font-size: 1.1rem;"></i>
                                <strong style="color: #198754;">You rated this order</strong>
                            </div>
                            <div style="display: flex; gap: 4px; margin-bottom: 8px;">
                                ${generateStarsDisplay(existingRating)}
                            </div>
                            ${existingReview ? `
                                <p style="margin: 8px 0 0; color: #495057; font-size: 0.9rem;">
                                    <i class="bi bi-chat-quote" style="color: #C9A961;"></i> "${escapeHtml(existingReview)}"
                                </p>
                            ` : ''}
                        </div>
                    `;
                } else {
                    // Show rating form
                    html += `
                        <div id="ratingSection${orderId}" style="background: #fff9f0; border: 2px solid #C9A961; border-radius: 8px; padding: 16px;">
                            <h6 style="margin: 0 0 12px; color: #1e4d2b; font-weight: 600;">
                                <i class="bi bi-star" style="color: #C9A961;"></i> Rate Your Experience
                            </h6>
                            <div style="display: flex; gap: 8px; margin-bottom: 12px; justify-content: center;">
                                ${generateRatingStars(orderId)}
                            </div>
                            <textarea 
                                id="reviewText${orderId}" 
                                placeholder="Share your experience with others (optional)..." 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; resize: vertical; font-family: 'Inter', sans-serif; font-size: 0.9rem;"
                                rows="3"
                            ></textarea>
                            <button 
                                onclick="submitRating(${orderId}, ${vendorId})"
                                class="btn"
                                style="width: 100%; margin-top: 12px; padding: 10px; background: #1e4d2b; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"
                                onmouseover="this.style.background='#2d6b3f'"
                                onmouseout="this.style.background='#1e4d2b'"
                            >
                                <i class="bi bi-send"></i> Submit Rating
                            </button>
                        </div>
                    `;
                }
                
                html += '</div>';
            }
            
            html += '</div>';
            
            // Add consultation section if we have items from a planner
            if (items.length > 0 && items[0].vendor_customer_id) {
                html += '<div style="padding: 16px; border-top: 2px solid var(--gray-light); margin-top: 12px;">';
                
                if (consultationBooked) {
                    // Show consultation status
                    const statusColors = {
                        'pending': '#ffc107',
                        'confirmed': '#198754',
                        'completed': '#6c757d',
                        'cancelled': '#dc3545'
                    };
                    const statusColor = statusColors[consultationStatus] || '#6c757d';
                    
                    html += `
                        <div style="background: ${statusColor}15; border: 2px solid ${statusColor}; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="bi bi-check-circle-fill" style="color: ${statusColor}; font-size: 1.2rem;"></i>
                                <strong style="color: ${statusColor};">Consultation Booked</strong>
                            </div>
                            <p style="margin: 0; color: var(--gray-dark); font-size: 0.9rem;">
                                Status: <strong>${consultationStatus.charAt(0).toUpperCase() + consultationStatus.slice(1)}</strong>
                            </p>
                        </div>
                        <a href="my_consultations.php" class="btn" style="width: 100%; padding: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; background: #1e4d2b; color: white; border: 2px solid #1e4d2b;">
                            <i class="bi bi-calendar-check"></i>
                            View My Consultations
                        </a>
                        <a href="book_consultation.php?planner_id=${items[0].vendor_customer_id}&order_id=${items[0].order_id || ''}" 
                           class="btn" 
                           style="width: 100%; padding: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; background: white; color: #1e4d2b; border: 2px solid #1e4d2b;">
                            <i class="bi bi-plus-circle"></i>
                            Book Another Consultation
                        </a>
                    `;
                } else {
                    // Show book consultation button
                    html += `
                        <a href="book_consultation.php?planner_id=${items[0].vendor_customer_id}&order_id=${items[0].order_id || ''}" 
                           class="btn" 
                           style="width: 100%; padding: 12px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; background: #1e4d2b; color: white; border: 2px solid #1e4d2b;">
                            <i class="bi bi-calendar-check"></i>
                            Book Consultation with Planner
                        </a>
                    `;
                }
                
                html += '</div>';
            }
            
            container.innerHTML = html;
        }

        /**
         * Show empty state
         */
        function showEmptyState() {
            document.getElementById('ordersContainer').innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-bag-x"></i>
                    </div>
                    <h3>No Orders Yet</h3>
                    <p class="text-muted">You haven't placed any orders yet</p>
                    <a href="all_products.php" class="btn" style="background: #1e4d2b; color: white; margin-top: 20px;">
                        <i class="bi bi-shop me-2"></i>Start Shopping
                    </a>
                </div>
            `;
        }

        /**
         * Get status class
         */
        function getStatusClass(status) {
            const statusMap = {
                'Paid': 'status-paid',
                'Pending': 'status-pending',
                'Completed': 'status-completed'
            };
            return statusMap[status] || 'status-pending';
        }

        /**
         * Format date
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
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

        /**
         * Generate interactive rating stars
         */
        function generateRatingStars(orderId) {
            let html = '';
            for (let i = 1; i <= 5; i++) {
                html += `
                    <i class="bi bi-star rating-star" 
                       data-order="${orderId}" 
                       data-rating="${i}"
                       style="font-size: 2rem; color: #ddd; cursor: pointer; transition: all 0.2s ease;"
                       onmouseover="highlightStars(${orderId}, ${i})"
                       onmouseout="resetStars(${orderId})"
                       onclick="selectRating(${orderId}, ${i})"
                    ></i>
                `;
            }
            return html;
        }

        /**
         * Generate static stars display for existing ratings
         */
        function generateStarsDisplay(rating) {
            let html = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
            
            for (let i = 0; i < fullStars; i++) {
                html += '<i class="bi bi-star-fill" style="color: #C9A961; font-size: 1.2rem;"></i>';
            }
            if (hasHalfStar) {
                html += '<i class="bi bi-star-half" style="color: #C9A961; font-size: 1.2rem;"></i>';
            }
            for (let i = 0; i < emptyStars; i++) {
                html += '<i class="bi bi-star" style="color: #ddd; font-size: 1.2rem;"></i>';
            }
            
            return html;
        }

        /**
         * Highlight stars on hover
         */
        function highlightStars(orderId, rating) {
            const stars = document.querySelectorAll(`.rating-star[data-order="${orderId}"]`);
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                    star.style.color = '#C9A961';
                } else {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                    star.style.color = '#ddd';
                }
            });
        }

        /**
         * Reset stars to selected rating
         */
        function resetStars(orderId) {
            const selectedRating = parseInt(document.querySelector(`#ratingSection${orderId}`)?.dataset.selectedRating || 0);
            if (selectedRating > 0) {
                highlightStars(orderId, selectedRating);
            } else {
                const stars = document.querySelectorAll(`.rating-star[data-order="${orderId}"]`);
                stars.forEach(star => {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                    star.style.color = '#ddd';
                });
            }
        }

        /**
         * Select a rating
         */
        function selectRating(orderId, rating) {
            const section = document.querySelector(`#ratingSection${orderId}`);
            if (section) {
                section.dataset.selectedRating = rating;
                highlightStars(orderId, rating);
            }
        }

        /**
         * Submit rating
         */
        function submitRating(orderId, vendorId) {
            const section = document.querySelector(`#ratingSection${orderId}`);
            const rating = parseInt(section?.dataset.selectedRating || 0);
            const reviewText = document.querySelector(`#reviewText${orderId}`)?.value.trim();
            
            if (rating === 0) {
                alert('Please select a rating before submitting');
                return;
            }
            
            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('rating', rating);
            if (reviewText) {
                formData.append('review_comment', reviewText);
            }
            
            // Disable submit button
            const submitBtn = section.querySelector('button');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            
            fetch('../actions/submit_rating_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Replace rating form with success message
                    section.innerHTML = `
                        <div style="text-align: center; color: #198754;">
                            <i class="bi bi-check-circle-fill" style="font-size: 2rem; margin-bottom: 8px;"></i>
                            <p style="margin: 0; font-weight: 600;">Thank you for your rating!</p>
                        </div>
                    `;
                    // Reload page after 2 seconds to show the rating
                    setTimeout(() => {
                        loadOrders();
                    }, 2000);
                } else {
                    alert(data.message || 'Failed to submit rating. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Rating';
                }
            })
            .catch(error => {
                console.error('Error submitting rating:', error);
                alert('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Rating';
            });
        }
    </script>
</body>
</html>
