<?php
/**
 * Submit Rating Action
 * Handles customer ratings and reviews for completed orders
 */

session_start();
header('Content-Type: application/json');

require_once '../settings/db_class.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to submit a rating']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Validate input
if (!isset($_POST['order_id']) || !isset($_POST['rating'])) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID and rating are required']);
    exit;
}

$order_id = (int)$_POST['order_id'];
$rating = floatval($_POST['rating']);
$review_comment = isset($_POST['review_comment']) ? trim($_POST['review_comment']) : null;

// Validate rating value (1-5)
if ($rating < 1 || $rating > 5) {
    echo json_encode(['status' => 'error', 'message' => 'Rating must be between 1 and 5']);
    exit;
}

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Verify the order belongs to this customer and hasn't been rated yet
    $verify_query = "SELECT order_id, rating, vendor_id, order_status 
                     FROM orders 
                     WHERE order_id = ? AND customer_id = ?";
    
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param('ii', $order_id, $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found or you do not have permission to rate this order']);
        exit;
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();
    
    // Check if order has already been rated
    if ($order['rating'] !== null) {
        echo json_encode(['status' => 'error', 'message' => 'You have already rated this order']);
        exit;
    }
    
    // Check if order is completed (optional - you may want to allow ratings for any order)
    // Uncomment the following lines if you only want completed orders to be rated
    /*
    if ($order['order_status'] !== 'Completed') {
        echo json_encode(['status' => 'error', 'message' => 'You can only rate completed orders']);
        exit;
    }
    */
    
    // Update the order with the rating
    $update_query = "UPDATE orders 
                     SET rating = ?, review_comment = ? 
                     WHERE order_id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('dsi', $rating, $review_comment, $order_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Thank you for your rating!',
            'rating' => $rating
        ]);
    } else {
        throw new Exception('Failed to save rating');
    }
    
} catch (Exception $e) {
    error_log("Rating submission error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while submitting your rating. Please try again.'
    ]);
}
?>
