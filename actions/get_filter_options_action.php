<?php
/**
 * Get Filter Options Action
 * Returns available categories and brands for filter dropdowns
 */

header('Content-Type: application/json');

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();
    
    $result = $controller->get_filter_options_ctr();
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
