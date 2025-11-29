<?php
/**
 * Get Vendor Reviews Action
 * Retrieves all reviews for a specific vendor
 */

header('Content-Type: application/json');

require_once '../settings/db_class.php';

if (!isset($_GET['vendor_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vendor ID is required']);
    exit;
}

$vendor_id = (int)$_GET['vendor_id'];

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Get all reviews for this vendor
    // Reviews are on orders that contain products from this vendor
    $query = "SELECT DISTINCT o.order_id, o.rating, o.review_comment, o.order_date,
                     c.customer_name, c.customer_image
              FROM orders o
              INNER JOIN orderdetails od ON o.order_id = od.order_id
              INNER JOIN products p ON od.product_id = p.product_id
              INNER JOIN customer c ON o.customer_id = c.customer_id
              WHERE p.user_id = ? AND o.rating IS NOT NULL AND o.rating > 0
              ORDER BY o.order_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            'order_id' => $row['order_id'],
            'rating' => floatval($row['rating']),
            'review_comment' => $row['review_comment'],
            'order_date' => $row['order_date'],
            'customer_name' => $row['customer_name'],
            'customer_image' => $row['customer_image']
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'status' => 'success',
        'reviews' => $reviews,
        'total' => count($reviews)
    ]);
    
} catch (Exception $e) {
    error_log("Get reviews error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load reviews'
    ]);
}
?>
