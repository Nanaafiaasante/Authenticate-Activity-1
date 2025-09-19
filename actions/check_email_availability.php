<?php

header('Content-Type: application/json');

require_once '../controllers/customer_controller.php';

$response = array();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['available'] = false;
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Get email from POST data
$email = trim($_POST['email'] ?? '');

// Validate email
if (empty($email)) {
    $response['available'] = false;
    $response['message'] = 'Email is required';
    echo json_encode($response);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['available'] = false;
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Check email availability
$available = check_email_availability_ctr($email);

$response['available'] = $available;
$response['message'] = $available ? 'Email is available' : 'Email is already registered';

echo json_encode($response);