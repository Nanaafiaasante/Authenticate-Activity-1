<?php
/**
 * Fetch Category Action - Retrieves all categories from the system for the logged-in user
 * Returns JSON response with category data
 */

header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

// Check if user is logged in
if (!check_login()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $user_id = get_user_id();
    $categoryController = new CategoryController();
    $response = $categoryController->get_all_categories_ctr([
        'user_id' => $user_id
    ]);
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
