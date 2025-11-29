<?php
/**
 * Get Consultation Details Action
 */

session_start();
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

if (!isset($_GET['consultation_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Consultation ID required'
    ]);
    exit;
}

$consultation_id = $_GET['consultation_id'];
$consultation = get_consultation_ctr($consultation_id);

if ($consultation) {
    // Verify user has access to this consultation
    if ($consultation['customer_id'] != $_SESSION['customer_id'] && $consultation['planner_id'] != $_SESSION['customer_id']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ]);
        exit;
    }
    
    echo json_encode([
        'status' => 'success',
        'consultation' => $consultation
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Consultation not found'
    ]);
}
?>
