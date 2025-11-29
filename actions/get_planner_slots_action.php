<?php
/**
 * Get Planner Slots Action
 * Retrieves all availability slots for the logged-in planner
 */

require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get planner_id from query parameter or session (for planner viewing their own)
if (isset($_GET['planner_id'])) {
    $planner_id = intval($_GET['planner_id']);
} elseif (isset($_SESSION['customer_id'])) {
    $planner_id = $_SESSION['customer_id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Planner ID required'
    ]);
    exit;
}

// Get slots
$slots = get_planner_slots_ctr($planner_id);

if ($slots) {
    echo json_encode([
        'status' => 'success',
        'slots' => $slots
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No slots found'
    ]);
}
?>
