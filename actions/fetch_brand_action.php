<?php
/**
 * Fetch Brand Action
 * Retrieves all brands created by the logged-in user
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

require_once '../controllers/brand_controller.php';

try {
    $controller = new BrandController();
    
    // Get all brands for the logged-in user
    $result = $controller->get_all_brands_ctr([
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
