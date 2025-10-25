<?php
/**
 * Fetch Product Action
 * Retrieves all products created by the logged-in user
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit();
}

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();
    
    // Get all products for the logged-in user
    $result = $controller->get_all_products_ctr([
        'user_id' => $_SESSION['customer_id']
    ]);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
