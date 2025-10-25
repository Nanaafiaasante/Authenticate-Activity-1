<?php
/**
 * Upload Product Image Action
 * Handles product image uploads to the uploads/ directory
 * Creates user and product subdirectories as needed
 * 
 * Directory structure: uploads/u{user_id}/p{product_id}/image_name.ext
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

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method'
        ]);
        exit();
    }

    // Check if file was uploaded
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No file uploaded'
        ]);
        exit();
    }

    // Check for upload errors
    if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'status' => 'error',
            'message' => 'File upload error: ' . $_FILES['product_image']['error']
        ]);
        exit();
    }

    $file = $_FILES['product_image'];
    $user_id = $_SESSION['customer_id'];
    $product_id = $_POST['product_id'] ?? 'temp_' . time(); // Use temp ID if product not yet created
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed'
        ]);
        exit();
    }

    // Validate file size (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file['size'] > $max_size) {
        echo json_encode([
            'status' => 'error',
            'message' => 'File size exceeds maximum limit of 5MB'
        ]);
        exit();
    }

    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Generate unique filename
    $new_filename = uniqid('img_') . '.' . $file_extension;
    
    // Define base upload directory (must be inside uploads/)
    $base_upload_dir = '../uploads';
    
    // Create user directory if it doesn't exist
    $user_dir = $base_upload_dir . '/u' . $user_id;
    if (!file_exists($user_dir)) {
        if (!mkdir($user_dir, 0755, true)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create user directory'
            ]);
            exit();
        }
    }
    
    // Create product directory if it doesn't exist
    $product_dir = $user_dir . '/p' . $product_id;
    if (!file_exists($product_dir)) {
        if (!mkdir($product_dir, 0755, true)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create product directory'
            ]);
            exit();
        }
    }
    
    // Verify the directory is inside uploads/ (security check)
    $real_product_dir = realpath($product_dir);
    $real_base_dir = realpath($base_upload_dir);
    
    if ($real_product_dir === false || strpos($real_product_dir, $real_base_dir) !== 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Security violation: Upload directory must be inside uploads/'
        ]);
        exit();
    }
    
    // Full path for the uploaded file
    $target_file = $product_dir . '/' . $new_filename;
    
    // Move uploaded file to target directory
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Return the relative path to store in database
        $relative_path = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $new_filename;
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Image uploaded successfully',
            'file_path' => $relative_path,
            'file_name' => $new_filename
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to move uploaded file'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
