<?php
/**
 * Empty Cart Action
 * Handles emptying/clearing all items from the cart
 */

session_start();
header('Content-Type: application/json');

// Include the cart controller
require_once(__DIR__ . '/../controllers/cart_controller.php');

// Check if request is POST or DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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
    // Empty the cart
    $result = empty_cart_ctr($customer_id, $ip_address);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => '0.00'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to clear cart. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
