<?php
/**
 * Update Consultation Status
 * Allows planner to confirm, complete, or cancel consultations
 */

session_start();
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['consultation_id']) || !isset($input['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$consultation_id = $input['consultation_id'];
$status = $input['status'];
$notes = $input['notes'] ?? '';
$planner_id = $_SESSION['customer_id'];

// Validate status
$valid_statuses = ['confirmed', 'completed', 'cancelled', 'no-show'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    exit;
}

// Verify consultation belongs to this planner
$consultation = get_consultation_ctr($consultation_id);
if (!$consultation || $consultation['planner_id'] != $planner_id) {
    echo json_encode(['status' => 'error', 'message' => 'Consultation not found or unauthorized']);
    exit;
}

// Update status
$result = update_consultation_status_ctr($consultation_id, $status, $notes);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Consultation status updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update consultation status'
    ]);
}
?>
