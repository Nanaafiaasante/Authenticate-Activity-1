<?php
/**
 * Delete Time Slot Action
 * Allows planners to delete their availability slots
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

// Get slot_id from query string
if (!isset($_GET['slot_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing slot ID'
    ]);
    exit;
}

$slot_id = intval($_GET['slot_id']);
$planner_id = $_SESSION['customer_id'];

// Delete slot (verify it belongs to this planner in the controller)
$result = delete_time_slot_ctr($slot_id, $planner_id);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Time slot deleted successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete time slot'
    ]);
}
?>
