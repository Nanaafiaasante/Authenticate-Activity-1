<?php
/**
 * Search Products Action
 * Searches all products based on query with optional pagination
 */

header('Content-Type: application/json');

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();
    
    // Get search query
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    
    if (empty($query)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search query is required'
        ]);
        exit;
    }
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    
    $offset = ($page - 1) * $per_page;
    
    // Search products
    $result = $controller->search_all_products_ctr([
        'query' => $query,
        'limit' => $per_page,
        'offset' => $offset
    ]);
    
    // Add pagination info
    if ($result['status'] === 'success') {
        $result['page'] = $page;
        $result['per_page'] = $per_page;
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
