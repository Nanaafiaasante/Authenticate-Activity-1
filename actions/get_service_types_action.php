<?php
// Catch all errors and return as JSON
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'PHP Error: ' . $errstr . ' in ' . basename($errfile) . ':' . $errline]);
    exit;
});

require_once '../controllers/consultation_controller.php';

header('Content-Type: application/json');

try {
    $services = get_service_types_ctr();

    if ($services) {
        echo json_encode(['status' => 'success', 'services' => $services]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No services found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
}
?>
