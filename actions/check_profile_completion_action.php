<?php
/**
 * Check Profile Completion Action
 * Checks if vendor has completed their profile
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

require_once '../controllers/customer_controller.php';

$customer_id = $_SESSION['customer_id'];
$vendor = get_customer_by_id_ctr($customer_id);

if (!$vendor) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

// Check required fields for profile completion
$required_fields = ['vendor_name', 'about', 'customer_contact'];
$is_complete = true;
$missing_fields = [];

foreach ($required_fields as $field) {
    if (empty($vendor[$field])) {
        $is_complete = false;
        $missing_fields[] = $field;
    }
}

// Profile picture is optional but recommended
$has_profile_picture = !empty($vendor['profile_picture']);

echo json_encode([
    'status' => 'success',
    'is_complete' => $is_complete,
    'missing_fields' => $missing_fields,
    'has_profile_picture' => $has_profile_picture,
    'vendor' => [
        'vendor_name' => $vendor['vendor_name'] ?? '',
        'about' => $vendor['about'] ?? '',
        'contact' => $vendor['customer_contact'] ?? '',
        'profile_picture' => $vendor['profile_picture'] ?? ''
    ]
]);
?>
