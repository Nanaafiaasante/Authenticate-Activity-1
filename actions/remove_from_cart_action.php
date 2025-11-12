<?php
/**
 * Remove from Cart Action
 * Handles removing products from the shopping cart
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

// Get POST data (support both form data and JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// If JSON parsing fails, try $_POST
if (json_last_error() !== JSON_ERROR_NONE) {
    $data = $_POST;
}

// Validate required fields
if (!isset($data['product_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product ID is required'
    ]);
    exit();
}

$product_id = intval($data['product_id']);

// Get customer ID from session (null for guests)
$customer_id = isset($_SESSION['customer_id']) ? intval($_SESSION['customer_id']) : null;

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Remove item from cart
    $result = remove_from_cart_ctr($product_id, $customer_id, $ip_address);

    if ($result) {
        // Get updated cart count and total
        $cart_count = get_cart_count_ctr($customer_id, $ip_address);
        $cart_total = get_cart_total_ctr($customer_id, $ip_address);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product removed from cart successfully',
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 2)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to remove product from cart. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
