<?php
// Catch all errors and return as JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'PHP Error: ' . $errstr . ' in ' . $errfile . ' on line ' . $errline]);
    exit;
});

session_start();
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['planner_id']) || !isset($input['service_id']) || !isset($input['consultation_date']) || !isset($input['consultation_time']) || !isset($input['fee'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$planner_id = $input['planner_id'];
$service_id = $input['service_id'];
$date = $input['consultation_date'];
$time = $input['consultation_time'];
$duration = $input['duration'] ?? 60;
$fee = $input['fee'];
$notes = $input['notes'] ?? '';
$location = $input['location'] ?? '';
$order_id = $input['order_id'] ?? null;

// Check if slot is available
if (!is_slot_available_ctr($planner_id, $date, $time, $duration)) {
    echo json_encode(['status' => 'error', 'message' => 'This time slot is not available. Please choose another time.']);
    exit;
}

// Create consultation
$consultation_id = create_consultation_ctr($customer_id, $planner_id, $service_id, $date, $time, $duration, $fee, $notes, $location, $order_id);

if ($consultation_id) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Consultation created successfully',
        'consultation_id' => $consultation_id
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create consultation']);
}
?>
