<?php
/**
 * View User Products Action
 * Returns products created by the logged-in user (admin dashboard)
 */

header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit();
}

require_once '../controllers/product_controller.php';

try {
    $controller = new ProductController();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $offset = ($page - 1) * $per_page;

    $result = $controller->view_user_products_ctr([
        'user_id' => (int)$_SESSION['customer_id'],
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