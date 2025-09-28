<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic server-side validation
if (empty($email) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Attempt to login the customer
$login_data = array(
    'email' => $email,
    'password' => $password
);

$customer = login_customer_ctr($login_data);

if ($customer) {
    // Login successful - start session
    $_SESSION['customer_id'] = $customer['customer_id'];
    $_SESSION['customer_name'] = $customer['customer_name'];
    $_SESSION['customer_email'] = $customer['customer_email'];
    $_SESSION['user_role'] = $customer['user_role'];
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful! Welcome back, ' . $customer['customer_name'];
    
    // Determine redirect based on user role
    if ($customer['user_role'] == 1) {
        // Admin - redirect to category management
        $response['redirect'] = '../admin/category.php';
    } else {
        // Regular customer
        $response['redirect'] = '../index.php';
    }
    
    $response['customer'] = array(
        'id' => $customer['customer_id'],
        'name' => $customer['customer_name'],
        'email' => $customer['customer_email'],
        'role' => $customer['user_role']
    );
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email or password. Please try again.';
}

echo json_encode($response);