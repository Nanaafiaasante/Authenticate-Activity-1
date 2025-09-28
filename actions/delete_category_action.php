<?php
/**
 * Delete Category Action - Deletes a category for the logged-in user
 * Receives POST data and returns JSON response
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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Only POST method allowed'
    ]);
    exit;
}

try {
    // Get POST data
    $cat_id = $_POST['cat_id'] ?? '';
    $user_id = get_user_id();
    
    $categoryController = new CategoryController();
    $response = $categoryController->delete_category_ctr([
        'cat_id' => $cat_id,
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
