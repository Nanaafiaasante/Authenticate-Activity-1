<?php
/**
 * Get Cart Action
 * Retrieves the current cart items for a user/guest
 */

session_start();
header('Content-Type: application/json');

// Include the cart controller
require_once(__DIR__ . '/../controllers/cart_controller.php');

// Check if request is GET or POST
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

// Get customer ID from session (null for guests)
$customer_id = isset($_SESSION['customer_id']) ? intval($_SESSION['customer_id']) : null;

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Get cart items
    $cart_items = get_user_cart_ctr($customer_id, $ip_address);
    $cart_count = get_cart_count_ctr($customer_id, $ip_address);
    $cart_total = get_cart_total_ctr($customer_id, $ip_address);

    if ($cart_items !== false) {
        echo json_encode([
            'status' => 'success',
            'cart_items' => $cart_items ? $cart_items : [],
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 2),
            'cart_total_raw' => $cart_total
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to retrieve cart items'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
