<?php
/**
 * Get Customer Consultations
 * Returns all consultations for logged-in customer
 */

session_start();
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

try {
    $consultations = get_customer_consultations_ctr($customer_id);
    
    if ($consultations) {
        echo json_encode([
            'status' => 'success',
            'consultations' => $consultations
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'consultations' => []
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load consultations: ' . $e->getMessage()
    ]);
}
?>
