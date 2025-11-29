<?php
/**
 * Update Vendor Profile Action
 * Updates vendor profile information
 */

session_start();
require_once '../settings/db_class.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to update profile']);
    exit;
}

$vendor_id = $_POST['vendor_id'] ?? 0;
$customer_id = $_SESSION['customer_id'];

// Verify user is updating their own profile
if ($vendor_id != $customer_id) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Get form data
$vendor_name = trim($_POST['vendor_name'] ?? '');
$city = trim($_POST['city'] ?? '');
$country = trim($_POST['country'] ?? '');
$about = trim($_POST['about'] ?? '');

// Handle profile picture upload
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = $_FILES['profile_picture']['name'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_size = $_FILES['profile_picture']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Validate file
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file_ext, $allowed_extensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.']);
        exit;
    }
    
    if ($file_size > $max_size) {
        echo json_encode(['status' => 'error', 'message' => 'File size too large. Maximum 2MB allowed.']);
        exit;
    }
    
    // Generate unique filename
    $new_filename = 'profile_' . $customer_id . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $profile_picture = 'uploads/profiles/' . $new_filename;
    }
}

// Update database
$db = new db_connection();
$conn = $db->db_conn();

// Build update query
$updates = [];
$params = [];
$types = '';

if (!empty($vendor_name)) {
    $updates[] = "vendor_name = ?";
    $params[] = $vendor_name;
    $types .= 's';
}

if (!empty($city)) {
    $updates[] = "customer_city = ?";
    $params[] = $city;
    $types .= 's';
}

if (!empty($country)) {
    $updates[] = "customer_country = ?";
    $params[] = $country;
    $types .= 's';
}

if (!empty($about)) {
    $updates[] = "about = ?";
    $params[] = $about;
    $types .= 's';
}

if ($profile_picture) {
    $updates[] = "profile_picture = ?";
    $params[] = $profile_picture;
    $types .= 's';
}

if (empty($updates)) {
    echo json_encode(['status' => 'error', 'message' => 'No data to update']);
    exit;
}

// Add customer_id for WHERE clause
$params[] = $customer_id;
$types .= 'i';

$sql = "UPDATE customer SET " . implode(', ', $updates) . " WHERE customer_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Profile updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update profile: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
