<?php
/**
 * Add Product Action
 * Receives data from product creation form and adds a new product
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

require_once '../controllers/product_controller.php';

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
    $product_cat = $_POST['product_cat'] ?? '';
    $product_brand = $_POST['product_brand'] ?? '';
    $product_title = $_POST['product_title'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $product_desc = $_POST['product_desc'] ?? '';
    $product_image = $_POST['product_image'] ?? ''; // This will be set after image upload
    $product_keywords = $_POST['product_keywords'] ?? '';

    // Validate required fields
    if (empty($product_cat) || empty($product_brand) || empty($product_title) || empty($product_price)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Category, brand, title, and price are required'
        ]);
        exit();
    }

    $controller = new ProductController();
    
    // Add product
    $result = $controller->add_product_ctr([
        'product_cat' => $product_cat,
        'product_brand' => $product_brand,
        'product_title' => $product_title,
        'product_price' => $product_price,
        'product_desc' => $product_desc,
        'product_image' => $product_image,
        'product_keywords' => $product_keywords,
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
