<?php
/**
 * Get Vendor Profile Action
 * Retrieves vendor profile information including ratings
 */

// Prevent any HTML output
ob_start();

require_once '../controllers/customer_controller.php';
require_once '../settings/db_class.php';

// Clear any accidental output
ob_end_clean();

header('Content-Type: application/json');

if (!isset($_GET['vendor_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vendor ID is required']);
    exit;
}

$vendor_id = (int)$_GET['vendor_id'];

// Get vendor information
$vendor = get_customer_by_id_ctr($vendor_id);

if (!$vendor) {
    echo json_encode(['status' => 'error', 'message' => 'Vendor not found']);
    exit;
}

// Initialize default rating values
$vendor['average_rating'] = 0;
$vendor['rating_count'] = 0;

// Try to get ratings from orders table
try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Check if rating column exists first
    $check_column = "SHOW COLUMNS FROM orders LIKE 'rating'";
    $result = $conn->query($check_column);
    
    if ($result && $result->num_rows > 0) {
        // Rating column exists, get the ratings
        // Get ratings from orders that contain products from this vendor
        $rating_query = "SELECT AVG(o.rating) as average_rating, COUNT(o.rating) as rating_count 
                         FROM orders o
                         INNER JOIN orderdetails od ON o.order_id = od.order_id
                         INNER JOIN products p ON od.product_id = p.product_id
                         WHERE p.user_id = ? AND o.rating IS NOT NULL AND o.rating > 0
                         GROUP BY p.user_id";
        
        $stmt = $conn->prepare($rating_query);
        if ($stmt) {
            $stmt->bind_param('i', $vendor_id);
            $stmt->execute();
            $rating_result = $stmt->get_result();
            
            if ($rating_result && $rating_data = $rating_result->fetch_assoc()) {
                $vendor['average_rating'] = floatval($rating_data['average_rating'] ?: 0);
                $vendor['rating_count'] = intval($rating_data['rating_count'] ?: 0);
            }
            $stmt->close();
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    // If there's an error, just use default values (0 rating)
    // Don't expose the error to the client
}

echo json_encode([
    'status' => 'success',
    'vendor' => $vendor
]);
exit;
?>
