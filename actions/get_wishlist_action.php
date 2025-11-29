<?php
/**
 * Get Wishlist Action
 * Retrieves all items in the user's wishlist
 */

session_start();
header('Content-Type: application/json');

require_once '../controllers/wishlist_controller.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please login to view your wishlist'
    ]);
    exit;
}

$customer_id = intval($_SESSION['customer_id']);

try {
    $wishlist_items = get_wishlist_items_ctr($customer_id);
    $wishlist_count = get_wishlist_count_ctr($customer_id);
    
    echo json_encode([
        'status' => 'success',
        'wishlist_items' => $wishlist_items ? $wishlist_items : [],
        'wishlist_count' => $wishlist_count
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
