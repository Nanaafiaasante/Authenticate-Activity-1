<?php
/**
 * Add Time Slot Action
 * Allows planners to add availability slots
 */

require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a planner
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate data
if (!isset($data['day_of_week']) || !isset($data['start_time']) || !isset($data['end_time'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields'
    ]);
    exit;
}

$planner_id = $_SESSION['customer_id'];
$day_of_week = intval($data['day_of_week']);
$start_time = $data['start_time'];
$end_time = $data['end_time'];

// Validate day of week (0-6)
if ($day_of_week < 0 || $day_of_week > 6) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid day of week'
    ]);
    exit;
}

// Validate time format (HH:MM)
if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $start_time) || 
    !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $end_time)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid time format'
    ]);
    exit;
}

// Ensure end time is after start time
if (strtotime($end_time) <= strtotime($start_time)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'End time must be after start time'
    ]);
    exit;
}

// Add slot
$result = add_time_slot_ctr($planner_id, $day_of_week, $start_time, $end_time);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Time slot added successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add time slot'
    ]);
}
?>
