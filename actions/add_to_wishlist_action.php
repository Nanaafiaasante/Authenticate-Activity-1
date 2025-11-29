<?php
/**
 * Add to Wishlist Action
 * Adds a product to the user's wishlist
 */

session_start();
header('Content-Type: application/json');

require_once '../controllers/wishlist_controller.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to add items to your wishlist'
    ]);
    exit;
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['product_id']) || empty($data['product_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product ID is required'
    ]);
    exit;
}

$customer_id = intval($_SESSION['customer_id']);
$product_id = intval($data['product_id']);

try {
    $result = add_to_wishlist_ctr($customer_id, $product_id);
    
    if ($result) {
        $wishlist_count = get_wishlist_count_ctr($customer_id);
        echo json_encode([
            'status' => 'success',
            'message' => 'Added to wishlist',
            'wishlist_count' => $wishlist_count
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add to wishlist'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
