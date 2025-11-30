<?php
session_start();

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);
$customer_name = $_SESSION['customer_name'] ?? '';
$user_role = $_SESSION['user_role'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>VendorConnect Ghana - Your Wedding Planning Partner</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/index.css">
</head>
<body>

<!-- VENDORCONNECT GHANA LOGO -->
<a href="index.php" class="vc-logo">
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

	<!-- Subtle accent dots -->
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>

	<div class="menu-tray">
		<?php if ($is_logged_in): ?>
			<!-- Logged in menu -->
			<span class="user-info">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</span>
			<?php if ($user_role != 1): ?>
				<a href="view/all_products.php" class="btn btn-sm btn-outline-primary">All Products</a>
			<?php endif; ?>
			<?php if ($user_role == 1): ?>
				<!-- Planner menu -->
				<a href="admin/dashboard.php" class="btn btn-sm btn-outline-info">Dashboard</a>
				<a href="admin/category.php" class="btn btn-sm btn-outline-info">Category</a>
				<a href="admin/brand.php" class="btn btn-sm btn-outline-info">Brand</a>
				<a href="admin/product.php" class="btn btn-sm btn-outline-info">Add Product</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php else: ?>
			<!-- Guest menu -->
			<span class="me-2">Menu:</span>
			<a href="view/all_products.php" class="btn btn-sm btn-outline-primary">All Products</a>
			<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
			<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
		<?php endif; ?>

	</div>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="container">
			<div class="hero-content">
				<h1 class="hero-title">Your Dream Wedding Starts Here</h1>
				<p class="hero-subtitle">Connect with Ghana's finest wedding vendors and event planners. From elegant décor to exceptional catering, find everything you need to make your special day unforgettable.</p>
				<div class="hero-cta">
					<a href="view/all_products.php" class="btn-primary-cta">Explore Vendors</a>
					<?php if (!$is_logged_in): ?>
						<a href="login/register.php" class="btn-secondary-cta">Get Started</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- About Section -->
	<section class="about-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">About VendorConnect Ghana</h2>
				<p class="section-description">Your trusted marketplace for wedding planning excellence</p>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="about-content">
						<h3>Who We Are</h3>
						<p>VendorConnect Ghana is the premier platform connecting couples with verified wedding vendors and event planners across Ghana. We understand that your wedding day is one of the most important moments of your life, and we're here to make the planning process seamless and enjoyable.</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="about-content">
						<h3>Our Mission</h3>
						<p>To modernize Ghana's wedding industry by providing a centralized platform. We empower planners with verified profiles and business tools to expand their reach, while offering couples a free, transparent service to discover and book reliable professionals, thereby raising industry standards and fostering trust.
</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- What We Offer Section -->
	<section class="services-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">What VendorConnect Can Do For You</h2>
				<p class="section-description">Everything you need to plan your perfect wedding</p>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-shop"></i>
						</div>
						<h4>Browse Vendor Products</h4>
						<p>Explore a wide range of wedding products and services from verified vendors. From décor to catering, find everything in one place.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-calendar-check"></i>
						</div>
						<h4>Book Consultations</h4>
						<p>Schedule one-on-one consultations with wedding planners to discuss your vision, budget, and requirements.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-cart-check"></i>
						</div>
						<h4>Seamless Shopping</h4>
						<p>Add items to your cart, manage your orders, and make secure payments through our integrated platform.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-geo-alt"></i>
						</div>
						<h4>Location-Based Search</h4>
						<p>Find vendors near you with our location-based search feature. Connect with local professionals who understand your needs.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-shield-check"></i>
						</div>
						<h4>Verified Vendors</h4>
						<p>All vendors are verified event planners with proven track records. Shop with confidence knowing you're working with professionals.</p>
					</div>
				</div>
				<div class="col-md-4">
					<div class="service-card">
						<div class="service-icon">
							<i class="bi bi-credit-card"></i>
						</div>
						<h4>Secure Payments</h4>
						<p>Make secure online payments through Paystack. Track your orders and manage transactions with ease.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Features Section -->
	<section class="features-section">
		<div class="container">
			<div class="section-header">
				<h2 class="section-title">Standout Features</h2>
				<p class="section-description">What makes VendorConnect Ghana special</p>
			</div>
			<div class="features-grid">
				<div class="feature-item">
					<div class="feature-number">01</div>
					<h4>Advanced Filtering</h4>
					<p>Search by category, brand, price range, and more. Find exactly what you're looking for with our powerful filtering system.</p>
				</div>
				<div class="feature-item">
					<div class="feature-number">02</div>
					<h4>Real-Time Availability</h4>
					<p>Check planner availability and book consultation slots in real-time. No more back-and-forth emails.</p>
				</div>
				<div class="feature-item">
					<div class="feature-number">03</div>
					<h4>Vendor Portfolios</h4>
					<p>Browse detailed vendor profiles with service locations, contact information, and product galleries.</p>
				</div>
				<div class="feature-item">
					<div class="feature-number">04</div>
					<h4>Order Management</h4>
					<p>Track your orders from purchase to delivery. View order history and manage all your wedding purchases in one place.</p>
				</div>
				<div class="feature-item">
					<div class="feature-number">05</div>
					<h4>Consultation Dashboard</h4>
					<p>Manage all your consultation bookings, view upcoming meetings, and track consultation history.</p>
				</div>
				<div class="feature-item">
					<div class="feature-number">06</div>
					<h4>Vendor Analytics</h4>
					<p>For vendors: Access sales analytics, consultation metrics, and performance insights to grow your business.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CTA Section -->
	<section class="cta-section">
		<div class="container">
			<div class="cta-content">
				<h2>Ready to Start Planning?</h2>
				<p>Join thousands of couples who found their perfect vendors on VendorConnect Ghana</p>
				<div class="cta-buttons">
					<a href="view/all_products.php" class="btn-primary-cta">Browse Vendors</a>
					<?php if (!$is_logged_in): ?>
						<a href="login/register.php" class="btn-secondary-cta">Create Account</a>
					<?php else: ?>
						<a href="admin/dashboard.php" class="btn-secondary-cta">Go to Dashboard</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer-section">
		<div class="container">
			<div class="footer-content">
				<div class="footer-logo">
					<div class="vc-logo-ring-footer"></div>
					<div class="footer-text">
						<div class="footer-brand">VendorConnect Ghana</div>
						<p>Your wedding, our passion</p>
					</div>
				</div>
				<div class="footer-links">
					<a href="view/all_products.php">Browse Products</a>
					<?php if (!$is_logged_in): ?>
						<a href="login/register.php">Register</a>
						<a href="login/login.php">Login</a>
					<?php else: ?>
						<a href="login/logout.php">Logout</a>
					<?php endif; ?>
				</div>
			</div>
			<div class="footer-bottom">
				<p>&copy; 2025 VendorConnect Ghana. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios@1.6.7/dist/axios.min.js"></script>
	<script>
	// Populate global filters and wire interactions
	document.addEventListener('DOMContentLoaded', function () {
		// Load options
		fetch('actions/get_filter_options_action.php')
			.then(r => r.json())
			.then(d => {
				if (d.status === 'success') {
					const payload = d.data || d;
					populateSelect('globalCategoryFilter', payload.categories || [], 'cat_id', 'cat_name', 'Category');
					populateSelect('globalBrandFilter', payload.brands || [], 'brand_id', 'brand_name', 'Brand');
				}
			});

		// On change navigate to All Products with params
		document.getElementById('globalCategoryFilter').addEventListener('change', navigateToAllProductsWithFilters);
		document.getElementById('globalBrandFilter').addEventListener('change', navigateToAllProductsWithFilters);
	});

	function populateSelect(id, list, valueKey, labelKey, placeholder) {
		const sel = document.getElementById(id);
		sel.innerHTML = `<option value="">${placeholder}</option>`;
		list.forEach(item => {
			const opt = document.createElement('option');
			opt.value = item[valueKey];
			opt.textContent = item[labelKey];
			sel.appendChild(opt);
		});
	}

	function navigateToAllProductsWithFilters() {
		const cat = document.getElementById('globalCategoryFilter').value;
		const brand = document.getElementById('globalBrandFilter').value;
		const params = new URLSearchParams();
		if (cat) params.set('category', cat);
		if (brand) params.set('brand', brand);
		const qs = params.toString();
		window.location.href = qs ? `view/all_products.php?${qs}` : 'view/all_products.php';
	}

	function submitGlobalSearch(e) {
		const input = document.getElementById('globalSearchInput');
		if (!input.value.trim()) {
			e.preventDefault();
			return false;
		}
		return true;
	}
	</script>
</body>
</html>