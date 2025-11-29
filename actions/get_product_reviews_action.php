<?php
/**
 * Get Product Reviews Action
 * Retrieves all reviews for a specific product
 */

header('Content-Type: application/json');
require_once '../settings/db_class.php';

// Validate input
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

$product_id = (int)$_GET['product_id'];

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit;
}

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Get reviews for this product from orders
    // Join orderdetails to find orders containing this product
    $query = "SELECT DISTINCT
                o.rating,
                o.review_comment,
                o.order_date,
                c.customer_name
              FROM orders o
              INNER JOIN orderdetails od ON o.order_id = od.order_id
              INNER JOIN customer c ON o.customer_id = c.customer_id
              WHERE od.product_id = ?
              AND o.rating IS NOT NULL
              AND o.rating > 0
              ORDER BY o.order_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            'rating' => $row['rating'],
            'review_text' => $row['review_comment'], // Map to expected field name
            'created_at' => $row['order_date'],
            'customer_name' => $row['customer_name']
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'status' => 'success',
        'reviews' => $reviews,
        'count' => count($reviews)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
