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
	<title>Home</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
	<link rel = "stylesheet" href = "css\main.css" >
</head>

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
<body>

	<!-- Subtle accent dots -->
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>

	<div class="menu-tray">
		<?php if ($is_logged_in): ?>
			<!-- Logged in menu -->
			<span class="user-info">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</span>
			<a href="view/all_products.php" class="btn btn-sm btn-outline-primary">All Products</a>
			<?php if ($user_role == 1): ?>
				<!-- Admin menu -->
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

	<div class="container">
		<div class="welcome-section <?php echo ($user_role == 1) ? 'admin' : ''; ?>">
			<?php if ($is_logged_in): ?>
				<h1>Welcome back!</h1>
				<p>Hello <?php echo htmlspecialchars($customer_name); ?>, you are successfully logged in to your account.</p>
				
				<?php if ($user_role == 1): ?>
					<div class="admin-badge">Administrator</div>
				<?php endif; ?>
				
			<?php else: ?>
				<h1>Welcome</h1>
				<p>Welcome to VendorConnect Ghana - Your gateway to finding the perfect wedding planner.</p>
			<?php endif; ?>
		</div>
	</div>

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