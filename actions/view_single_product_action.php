<?php
/**
 * View Single Product Action
 * Retrieves detailed information for a specific product
 */

header('Content-Type: application/json');

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();
    
    // Get product ID
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    $result = $controller->view_single_product_ctr([
        'product_id' => $product_id
    ]);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
