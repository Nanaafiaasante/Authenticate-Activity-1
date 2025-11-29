<?php
/**
 * Get User Orders Action
 * Fetches all orders for the logged-in customer
 */

session_start();
require_once '../controllers/order_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Get customer orders
$orders = get_user_orders_ctr($customer_id);

if ($orders) {
    // Calculate item count for each order
    foreach ($orders as &$order) {
        $details = get_order_details_ctr($order['order_id']);
        $order['item_count'] = count($details);
        $order['currency'] = 'GHS'; // Default currency
    }
    
    echo json_encode([
        'status' => 'success',
        'orders' => $orders
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No orders found'
    ]);
}
?>
