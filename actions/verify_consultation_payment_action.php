<?php
session_start();
require_once '../controllers/consultation_controller.php';
require_once '../settings/paystack_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if (!isset($_GET['reference']) || !isset($_GET['consultation_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing payment reference or consultation ID']);
    exit;
}

$reference = $_GET['reference'];
$consultation_id = $_GET['consultation_id'];

// Verify payment with Paystack
$verification = paystack_verify_transaction($reference);

if ($verification && $verification['status'] === true && $verification['data']['status'] === 'success') {
    // Update consultation payment status
    $updated = update_consultation_payment_ctr($consultation_id, $reference, 'paid');
    
    if ($updated) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Payment verified successfully',
            'consultation_id' => $consultation_id
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update payment status']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payment verification failed']);
}
?>
