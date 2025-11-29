<?php
/**
 * Add to Cart Action
 * Handles adding products to the shopping cart
 */

session_start();
header('Content-Type: application/json');

// Check if user is a planner (planners cannot add to cart)
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Event planners cannot purchase items. Only customers can add items to cart.'
    ]);
    exit;
}

// Include the cart controller
require_once(__DIR__ . '/../controllers/cart_controller.php');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
if (!isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product ID and quantity are required'
    ]);
    exit();
}

$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$selected_items = isset($data['selected_items']) ? $data['selected_items'] : [];

// Validate quantity
if ($quantity <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid quantity. Must be greater than 0'
    ]);
    exit();
}

// Get customer ID from session (null for guests)
$customer_id = isset($_SESSION['customer_id']) ? intval($_SESSION['customer_id']) : null;

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Add item to cart with selected package items
    $result = add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity, $selected_items);

    if ($result) {
        // Get updated cart count
        $cart_count = get_cart_count_ctr($customer_id, $ip_address);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart_count
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add product to cart. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
