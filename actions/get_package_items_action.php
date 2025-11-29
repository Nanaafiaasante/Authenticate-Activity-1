<?php
/**
 * Get Package Items Action
 * Retrieves all package items for a specific product
 */

header('Content-Type: application/json');

require_once '../settings/db_class.php';

// Validate input
if (!isset($_GET['product_id'])) {
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
    
    // Get all package items for this product
    $query = "SELECT item_id, product_id, item_name, is_optional, created_at
              FROM product_package_items
              WHERE product_id = ?
              ORDER BY created_at ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'item_id' => $row['item_id'],
            'product_id' => $row['product_id'],
            'item_name' => $row['item_name'],
            'is_optional' => (int)$row['is_optional'],
            'created_at' => $row['created_at']
        ];
    }
    
    $stmt->close();
    $conn->close();
    
    echo json_encode([
        'status' => 'success',
        'items' => $items,
        'total' => count($items)
    ]);
    
} catch (Exception $e) {
    error_log("Get package items error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while loading package items'
    ]);
}
?>
