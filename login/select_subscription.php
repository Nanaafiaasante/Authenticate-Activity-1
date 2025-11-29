<?php
// Validate that user came from role selection with planner role
if (!isset($_GET['role']) || $_GET['role'] !== 'planner') {
    header('Location: select_role.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Plan - VendorConnect Ghana</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/select_subscription.css">
</head>
<body>
    <!-- Header -->
    

    <!-- Decorative Elements -->
    <div class="decorative-dots top-right"></div>
    <div class="decorative-dots bottom-left"></div>

    <!-- Main Content -->
    <div class="subscription-container">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header text-center">
                <h1 class="page-title">Choose Your Planner Tier</h1>
                <p class="page-subtitle">Select the perfect plan to grow your wedding planning business</p>
                <div class="step-indicator">
                    <span class="step completed">1. Role</span>
                    <span class="step-divider">→</span>
                    <span class="step active">2. Subscription</span>
                    <span class="step-divider">→</span>
                    <span class="step">3. Register</span>
                </div>
            </div>

            <!-- Pricing Cards -->
            <div class="row pricing-row">
                <!-- Starter Tier - $99 -->
                <div class="col-lg-6 mb-4">
                    <div class="pricing-card starter-tier">
                        <div class="tier-badge">
                            <i class="bi bi-star"></i> Starter
                        </div>
                        <div class="pricing-header">
                            <h2 class="tier-name">Basic Planner</h2>
                            <div class="price-display">
                                <span class="currency">GHS</span>
                                <span class="amount">99</span>
                                <span class="period">/month</span>
                            </div>
                            <p class="tier-description">Perfect for independent planners starting their journey</p>
                        </div>
                        
                        <div class="features-section">
                            <h3 class="features-title">What's Included:</h3>
                            <ul class="features-list">
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Up to 25 Products</strong>
                                        <span>List vendors, venues & services</span>
                                    </div>
                                </li>
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Basic Analytics Dashboard</strong>
                                        <span>Track views & engagement</span>
                                    </div>
                                </li>
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>5 Portfolio Galleries</strong>
                                        <span>Showcase your best work</span>
                                    </div>
                                </li>
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Standard Support</strong>
                                        <span>Email support (48hr response)</span>
                                    </div>
                                </li>
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Client Inquiry Management</strong>
                                        <span>Organize leads & requests</span>
                                    </div>
                                </li>
                                <li class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Basic Brand Profile</strong>
                                        <span>Custom bio & contact info</span>
                                    </div>
                                </li>
                                <li class="feature-item limited">
                                    <i class="bi bi-x-circle"></i>
                                    <div class="feature-content">
                                        <strong>Priority Search Placement</strong>
                                        <span>Premium tier only</span>
                                    </div>
                                </li>
                                <li class="feature-item limited">
                                    <i class="bi bi-x-circle"></i>
                                    <div class="feature-content">
                                        <strong>Advanced Marketing Tools</strong>
                                        <span>Premium tier only</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <a href="register.php?role=planner&tier=starter" class="btn-select-tier starter">
                            Select Basic Plan
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <div class="tier-guarantee">
                            <i class="bi bi-shield-check"></i>
                            <span>Cancel anytime • No setup fees</span>
                        </div>
                    </div>
                </div>

                <!-- Premium Tier - $199 -->
                <div class="col-lg-6 mb-4">
                    <div class="pricing-card premium-tier featured">
                        <div class="popular-badge">
                            <i class="bi bi-lightning-fill"></i> Most Popular
                        </div>
                        <div class="tier-badge premium">
                            <i class="bi bi-gem"></i> Premium
                        </div>
                        <div class="pricing-header">
                            <h2 class="tier-name">Premium Planner</h2>
                            <div class="price-display">
                                <span class="currency">GHS</span>
                                <span class="amount">199</span>
                                <span class="period">/month</span>
                            </div>
                            <p class="tier-description">For established planners ready to scale and dominate</p>
                        </div>
                        
                        <div class="features-section">
                            <h3 class="features-title">Everything in Basic, plus:</h3>
                            <ul class="features-list">
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Unlimited Products</strong>
                                        <span>No limits on your offerings</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Advanced Analytics Suite</strong>
                                        <span>Revenue tracking & conversion insights</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Unlimited Portfolio Galleries</strong>
                                        <span>Showcase all your events</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Priority Support</strong>
                                        <span>Chat & phone (4hr response)</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Featured Search Placement</strong>
                                        <span>Top of search results</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Premium Brand Profile</strong>
                                        <span>Video intro, testimonials & badges</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Marketing Automation</strong>
                                        <span>Email campaigns & follow-ups</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Client Booking System</strong>
                                        <span>Calendar integration & reminders</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Social Media Integration</strong>
                                        <span>Auto-post to Instagram & Facebook</span>
                                    </div>
                                </li>
                                <li class="feature-item premium-feature">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div class="feature-content">
                                        <strong>Monthly Performance Report</strong>
                                        <span>Detailed insights & recommendations</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <a href="register.php?role=planner&tier=premium" class="btn-select-tier premium">
                            Select Premium Plan
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <div class="tier-guarantee premium">
                            <i class="bi bi-shield-check"></i>
                            <span>Cancel anytime • Priority onboarding included</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Link -->
            <div class="text-center mt-5">
                <a href="select_role.php" class="back-link">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to Role Selection
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="subscription-footer">
        <div class="container">
            <p>&copy; 2025 VendorConnect Ghana. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
