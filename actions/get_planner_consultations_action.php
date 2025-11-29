<?php
/**
 * Get Planner Consultations Action
 */

session_start();
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit;
}

$planner_id = $_SESSION['customer_id'];
$status = isset($_GET['status']) ? $_GET['status'] : null;

$consultations = get_planner_consultations_ctr($planner_id, $status);

if ($consultations) {
    echo json_encode([
        'status' => 'success',
        'consultations' => $consultations
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No consultations found'
    ]);
}
?>
