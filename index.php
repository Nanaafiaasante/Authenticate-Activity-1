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
	<style>
		/* Clean background with subtle nude/pink tones */
		body {
			background: linear-gradient(135deg, #faf7f5 0%, #f5f0ed 50%, #f0ebe8 100%);
			min-height: 100vh;
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
			color: #6b5b5b;
		}

		/* Refined menu tray */
		.menu-tray {
			position: fixed;
			top: 16px;
			right: 16px;
			background: rgba(255, 253, 252, 0.9);
			border: 1px solid rgba(234, 224, 218, 0.5);
			border-radius: 12px;
			padding: 12px 18px;
			box-shadow: 0 4px 20px rgba(139, 118, 108, 0.08);
			z-index: 1000;
			backdrop-filter: blur(8px);
		}

		.menu-tray span {
			color: #8b766c;
			font-weight: 500;
			font-size: 0.9rem;
		}

		.menu-tray a { 
			margin-left: 8px;
			font-weight: 500;
			font-size: 0.875rem;
		}

		.user-info {
			color: #8b766c;
			font-size: 0.9rem;
			margin-right: 10px;
			font-weight: 500;
		}

		/* Custom button styling */
		.btn-outline-primary {
			color: #c4967c;
			border-color: #c4967c;
			background: transparent;
		}

		.btn-outline-primary:hover {
			background-color: #c4967c;
			border-color: #c4967c;
			color: white;
		}

		.btn-outline-secondary {
			color: #a0857a;
			border-color: #d4c0b8;
			background: transparent;
		}

		.btn-outline-secondary:hover {
			background-color: #a0857a;
			border-color: #a0857a;
			color: white;
		}

		.btn-outline-info {
			color: #7c9bb5;
			border-color: #7c9bb5;
			background: transparent;
		}

		.btn-outline-info:hover {
			background-color: #7c9bb5;
			border-color: #7c9bb5;
			color: white;
		}

		.btn-outline-danger {
			color: #c67c7c;
			border-color: #c67c7c;
			background: transparent;
		}

		.btn-outline-danger:hover {
			background-color: #c67c7c;
			border-color: #c67c7c;
			color: white;
		}

		/* Decorative corner elements for the box */
		.welcome-section::before {
			content: '✨';
			position: absolute;
			top: 15px;
			left: 20px;
			font-size: 1.2rem;
			opacity: 0.6;
		}

		.welcome-section::after {
			content: '✨';
			position: absolute;
			bottom: 15px;
			right: 20px;
			font-size: 1.2rem;
			opacity: 0.6;
		}

		/* Admin welcome decorations */
		.welcome-section.admin::before {
			content: '👑';
		}

		.welcome-section.admin::after {
			content: '👑';
		}

		/* Main container */
		.container {
			padding-top: 120px;
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: calc(100vh - 120px);
		}

		/* Cute welcome box */
		.welcome-section {
			text-align: center;
			max-width: 500px;
			padding: 50px 40px;
			position: relative;
			background: rgba(255, 255, 255, 0.7);
			border-radius: 24px;
			box-shadow: 0 10px 40px rgba(139, 118, 108, 0.08);
			border: 1px solid rgba(234, 224, 218, 0.6);
			backdrop-filter: blur(10px);
		}

		.welcome-section h1 {
			font-family: 'Playfair Display', serif;
			font-size: 3.8rem;
			font-weight: 600;
			color: #8b766c;
			margin-bottom: 20px;
			letter-spacing: -0.02em;
			text-shadow: 0 2px 4px rgba(139, 118, 108, 0.05);
		}

		.welcome-section p {
			font-family: 'Inter', sans-serif;
			font-size: 1.1rem;
			color: #a0857a;
			font-weight: 400;
			line-height: 1.7;
			margin-bottom: 0;
			opacity: 0.85;
		}

		/* Admin badge */
		.admin-badge {
			display: inline-block;
			background: linear-gradient(135deg, #7c9bb5, #a8c5d8);
			color: white;
			padding: 4px 12px;
			border-radius: 12px;
			font-size: 0.8rem;
			font-weight: 600;
			margin-top: 15px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			box-shadow: 0 2px 8px rgba(124, 155, 181, 0.3);
		}

		/* Subtle accent elements */
		.accent-dot {
			position: absolute;
			width: 2px;
			height: 2px;
			background-color: #d4c0b8;
			border-radius: 50%;
		}

		.accent-dot:nth-child(1) {
			top: 25%;
			left: 20%;
		}

		.accent-dot:nth-child(2) {
			top: 70%;
			right: 15%;
		}

		.accent-dot:nth-child(3) {
			bottom: 30%;
			left: 10%;
		}

		/* Responsive design */
		@media (max-width: 768px) {
			.menu-tray {
				top: 12px;
				right: 12px;
				padding: 10px 14px;
			}
			
			.menu-tray span, .user-info {
				font-size: 0.85rem;
			}
			
			.welcome-section {
				max-width: 400px;
				padding: 40px 30px;
				margin: 0 20px;
			}
			
			.welcome-section h1 {
				font-size: 3.2rem;
			}
			
			.welcome-section p {
				font-size: 1.05rem;
			}
		}

		@media (max-width: 480px) {
			.menu-tray span:not(.user-info) {
				display: none;
			}
			
			.user-info {
				display: block;
				margin-bottom: 5px;
				margin-right: 0;
			}
			
			.welcome-section {
				padding: 35px 25px;
				margin: 0 15px;
				border-radius: 20px;
			}
			
			.welcome-section h1 {
				font-size: 2.8rem;
			}
			
			.welcome-section::before,
			.welcome-section::after {
				font-size: 1rem;
			}
		}
	</style>
</head>
<body>

	<!-- Subtle accent dots -->
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>
	<div class="accent-dot"></div>

	<div class="menu-tray">
		<?php if ($is_logged_in): ?>
			<!-- Logged in menu -->
			<span class="user-info">Welcome, <?php echo htmlspecialchars($customer_name); ?>!</span>
			<?php if ($user_role == 1): ?>
				<a href="admin/category.php" class="btn btn-sm btn-outline-info">Category</a>
			<?php endif; ?>
			<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
		<?php else: ?>
			<!-- Guest menu -->
			<span class="me-2">Menu:</span>
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
				<p>Use the menu in the top-right to Register or Login.</p>
			<?php endif; ?>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>