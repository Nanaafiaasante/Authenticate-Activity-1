<?php
/**
 * View User Products Action
 * Returns products created by a specific vendor or the logged-in user (admin dashboard)
 */

header('Content-Type: application/json');

session_start();

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $offset = ($page - 1) * $per_page;

    // Check if vendor_id is passed (for public profile viewing)
    // Otherwise use the logged-in user's ID (for admin dashboard)
    if (isset($_GET['vendor_id'])) {
        $user_id = (int)$_GET['vendor_id'];
    } elseif (isset($_SESSION['customer_id'])) {
        $user_id = (int)$_SESSION['customer_id'];
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in'
        ]);
        exit();
    }

    $result = $controller->view_user_products_ctr([
        'user_id' => $user_id,
        'limit' => $per_page,
        'offset' => $offset
    ]);

    if ($result['status'] === 'success') {
        $result['page'] = $page;
        $result['per_page'] = $per_page;
        $result['total_pages'] = $result['total'] ? (int)ceil($result['total'] / $per_page) : 1;
    }

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>