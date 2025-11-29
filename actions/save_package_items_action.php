<?php
/**
 * Save Package Items Action
 * Handles saving/updating package items for a product
 */

session_start();
header('Content-Type: application/json');

require_once '../settings/db_class.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['customer_id']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Validate input
if (!isset($_POST['product_id']) || !isset($_POST['items'])) {
    echo json_encode(['status' => 'error', 'message' => 'Product ID and items are required']);
    exit;
}

$product_id = (int)$_POST['product_id'];
$items_json = $_POST['items'];
$items = json_decode($items_json, true);

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit;
}

try {
    $db = new db_connection();
    $conn = $db->db_conn();
    
    // Verify the product belongs to this user
    $verify_query = "SELECT product_id FROM products WHERE product_id = ? AND user_id = ?";
    $stmt = $conn->prepare($verify_query);
    $stmt->bind_param('ii', $product_id, $_SESSION['customer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found or access denied']);
        exit;
    }
    $stmt->close();
    
    // Delete existing package items for this product
    $delete_query = "DELETE FROM product_package_items WHERE product_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->close();
    
    // Insert new package items
    if (!empty($items) && is_array($items)) {
        $insert_query = "INSERT INTO product_package_items (product_id, item_name, is_optional) 
                         VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        
        foreach ($items as $item) {
            $item_name = trim($item['item_name']);
            $is_optional = isset($item['is_optional']) ? (int)$item['is_optional'] : 1;
            
            if (!empty($item_name)) {
                $stmt->bind_param('isi', $product_id, $item_name, $is_optional);
                $stmt->execute();
            }
        }
        
        $stmt->close();
    }
    
    $conn->close();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Package items saved successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Save package items error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while saving package items'
    ]);
}
?>
