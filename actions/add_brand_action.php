<?php
/**
 * Add Brand Action
 * Receives data from brand creation form and adds a new brand
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

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized: Admin access required'
    ]);
    exit();
}

require_once '../controllers/brand_controller.php';

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method'
        ]);
        exit();
    }

    // Get POST data
    $brand_name = $_POST['brand_name'] ?? '';

    // Validate required fields
    if (empty($brand_name)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand name is required'
        ]);
        exit();
    }

    $controller = new BrandController();
    
    // Add brand
    $result = $controller->add_brand_ctr([
        'brand_name' => $brand_name,
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
