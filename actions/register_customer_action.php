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
$user_role = 1; // Default to customer role

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

// Check if email is already registered
if (!check_email_availability_ctr($customer_email)) {
    $response['status'] = 'error';
    $response['message'] = 'Email is already registered';
    echo json_encode($response);
    exit();
}

// Prepare data for registration
$customer_data = [
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'customer_pass' => $customer_pass,
    'customer_country' => $customer_country,
    'customer_city' => $customer_city,
    'customer_contact' => $customer_contact,
    'user_role' => $user_role
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