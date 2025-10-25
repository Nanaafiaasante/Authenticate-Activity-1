<?php
/**
 * View All Products Action
 * Retrieves all products for customer view with optional pagination
 */

header('Content-Type: application/json');

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    
    $offset = ($page - 1) * $per_page;
    
    // Get all products
    $result = $controller->view_all_products_ctr([
        'limit' => $per_page,
        'offset' => $offset
    ]);
    
    // Add pagination info
    if ($result['status'] === 'success') {
        $result['page'] = $page;
        $result['per_page'] = $per_page;
        $result['total_pages'] = ceil($result['total'] / $per_page);
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
