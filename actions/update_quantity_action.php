<?php
/**
 * Update Cart Quantity Action
 * Handles updating the quantity of products in the cart
 */

session_start();
header('Content-Type: application/json');

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

// Validate quantity
if ($quantity < 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid quantity'
    ]);
    exit();
}

// Get customer ID from session (null for guests)
$customer_id = isset($_SESSION['customer_id']) ? intval($_SESSION['customer_id']) : null;

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Update cart item quantity (if 0, it will be removed)
    $result = update_cart_item_ctr($product_id, $customer_id, $ip_address, $quantity);

    if ($result) {
        // Get updated cart count and total
        $cart_count = get_cart_count_ctr($customer_id, $ip_address);
        $cart_total = get_cart_total_ctr($customer_id, $ip_address);
        
        // Get updated cart items to calculate item subtotal
        $cart_items = get_user_cart_ctr($customer_id, $ip_address);
        $item_subtotal = 0;
        
        foreach ($cart_items as $item) {
            if ($item['p_id'] == $product_id) {
                $item_subtotal = $item['subtotal'];
                break;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => $quantity > 0 ? 'Cart updated successfully' : 'Item removed from cart',
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 2),
            'item_subtotal' => number_format($item_subtotal, 2),
            'quantity' => $quantity
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update cart. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
