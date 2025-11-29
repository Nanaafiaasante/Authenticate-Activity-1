<?php
/**
 * Get Planner Analytics Action
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
$analytics = get_planner_analytics_ctr($planner_id);

if ($analytics) {
    echo json_encode([
        'status' => 'success',
        'analytics' => $analytics
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to load analytics'
    ]);
}
?>
