<?php
/**
 * Get Order Details Action
 * Fetches order items and details
 */

session_start();
require_once '../controllers/order_controller.php';
require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Get order ID
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid order ID'
    ]);
    exit;
}

// Get order details
$items = get_order_details_ctr($order_id);

if ($items) {
    // Calculate item totals
    foreach ($items as &$item) {
        $item['item_total'] = $item['qty'] * $item['product_price'];
    }
    
    // Check if consultation has been booked for this order
    $consultation_booked = false;
    $consultation_status = null;
    
    // Query consultations table for this order_id
    require_once '../classes/consultation_class.php';
    $consultation_class = new consultation_class();
    $conn = $consultation_class->db_conn();
    $order_id_escaped = mysqli_real_escape_string($conn, $order_id);
    $sql = "SELECT consultation_id, booking_status FROM consultations WHERE order_id = $order_id_escaped LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $consultation = mysqli_fetch_assoc($result);
        $consultation_booked = true;
        $consultation_status = $consultation['booking_status'];
    }
    
    echo json_encode([
        'status' => 'success',
        'items' => $items,
        'consultation_booked' => $consultation_booked,
        'consultation_status' => $consultation_status
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No items found'
    ]);
}
?>
