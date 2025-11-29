<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['customer_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';
require_once '../classes/location_class.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Get form data
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_email = trim($_POST['customer_email'] ?? '');
$customer_pass = $_POST['customer_pass'] ?? '';
$customer_country = trim($_POST['customer_country'] ?? '');
$customer_city = trim($_POST['customer_city'] ?? '');
$customer_contact = trim($_POST['customer_contact'] ?? '');
$user_role = isset($_POST['user_role']) ? (int)$_POST['user_role'] : 2; // Default to couple/customer role (2)
$subscription_tier = isset($_POST['subscription_tier']) ? trim($_POST['subscription_tier']) : null; // For planners only
$subscription_status = isset($_POST['subscription_status']) ? trim($_POST['subscription_status']) : null; // Subscription status

// Basic server-side validation
if (empty($customer_name) || empty($customer_email) || empty($customer_pass) || 
    empty($customer_country) || empty($customer_city) || empty($customer_contact)) {
    $response['status'] = 'error';
    $response['message'] = 'All fields are required';
    echo json_encode($response);
    exit();
}

// Validate email format
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Validate password strength
if (strlen($customer_pass) < 6) {
    $response['status'] = 'error';
    $response['message'] = 'Password must be at least 6 characters long';
    echo json_encode($response);
    exit();
}

// Validate name format (letters, spaces, hyphens, apostrophes only)
if (!preg_match("/^[A-Za-z\s'-]+$/", $customer_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Name should only contain letters, spaces, hyphens, and apostrophes';
    echo json_encode($response);
    exit();
}

if (strlen($customer_name) < 2) {
    $response['status'] = 'error';
    $response['message'] = 'Name must be at least 2 characters long';
    echo json_encode($response);
    exit();
}

// Validate international phone number format (e.g., +233 XX XXX XXXX, +1 555 123 4567, +234 802 123 4567)
$phone_clean = preg_replace('/\s+/', '', $customer_contact);
// Accept international format: + followed by 1-4 digits (country code) and 6-15 digits (number)
if (!preg_match("/^\+[0-9]{1,4}[0-9]{6,15}$/", $phone_clean)) {
    $response['status'] = 'error';
    $response['message'] = 'Please enter a valid international phone number (e.g., +233 24 123 4567)';
    echo json_encode($response);
    exit();
}

// Validate user role
if (!in_array($user_role, [1, 2])) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid user role';
    echo json_encode($response);
    exit();
}

// Validate subscription tier for planners
if ($user_role == 1) {
    if (!$subscription_tier || !in_array($subscription_tier, ['starter', 'premium'])) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid subscription tier';
        echo json_encode($response);
        exit();
    }
}

// Check if email is already registered
if (!check_email_availability_ctr($customer_email)) {
    $response['status'] = 'error';
    $response['message'] = 'Email is already registered';
    echo json_encode($response);
    exit();
}

// Geocode location to get coordinates
$coordinates = Location::geocodeLocation($customer_city, $customer_country);
$latitude = null;
$longitude = null;

if ($coordinates) {
    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];
}

// Prepare data for registration
$customer_data = [
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'customer_pass' => $customer_pass,
    'customer_country' => $customer_country,
    'customer_city' => $customer_city,
    'customer_contact' => $customer_contact,
    'user_role' => $user_role,
    'subscription_tier' => $subscription_tier,
    'subscription_status' => ($user_role == 1) ? ($subscription_status ?? 'pending') : null,
    'latitude' => $latitude,
    'longitude' => $longitude
];

// Attempt to register the customer
$customer_id = register_customer_ctr($customer_data);

if ($customer_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registration successful! Please login to continue.';
    $response['customer_id'] = $customer_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Registration failed. Please try again.';
}

echo json_encode($response);