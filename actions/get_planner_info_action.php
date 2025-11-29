<?php
session_start();
require_once '../controllers/customer_controller.php';

header('Content-Type: application/json');

if (!isset($_GET['planner_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Planner ID required']);
    exit;
}

$planner_id = $_GET['planner_id'];
$planner = get_customer_by_id_ctr($planner_id);

if ($planner && $planner['user_role'] == 1) {
    echo json_encode(['status' => 'success', 'planner' => $planner]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Planner not found']);
}
?>
