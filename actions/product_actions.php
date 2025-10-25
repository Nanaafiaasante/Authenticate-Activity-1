<?php
/**
 * Product Actions Router
 * Unified endpoint to access existing modular product actions via `action` param.
 * Supported actions:
 *  - view_all        -> view_all_products_action.php
 *  - search          -> search_products_action.php
 *  - filter          -> filter_products_action.php
 *  - single          -> view_single_product_action.php
 *  - options         -> get_filter_options_action.php
 */

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$map = [
    'view_all' => __DIR__ . '/view_all_products_action.php',
    'search'   => __DIR__ . '/search_products_action.php',
    'filter'   => __DIR__ . '/filter_products_action.php',
    'single'   => __DIR__ . '/view_single_product_action.php',
    'options'  => __DIR__ . '/get_filter_options_action.php',
];

if (!$action || !isset($map[$action])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid or missing action. Use one of: ' . implode(', ', array_keys($map))
    ]);
    exit;
}

// Defer to the existing action file (outputs JSON and exits)
require $map[$action];
