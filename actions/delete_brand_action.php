<?php
/**
 * Delete Brand Action
 * Receives brand ID and deletes the brand
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
    $brand_id = $_POST['brand_id'] ?? '';

    // Validate required field
    if (empty($brand_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Brand ID is required'
        ]);
        exit();
    }

    $controller = new BrandController();
    
    // Delete brand
    $result = $controller->delete_brand_ctr([
        'brand_id' => $brand_id,
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
