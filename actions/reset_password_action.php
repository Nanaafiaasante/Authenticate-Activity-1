<?php
header('Content-Type: application/json');

require_once '../settings/db_class.php';

$response = array();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Get data from request
$token = trim($_POST['token'] ?? '');
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($token)) {
    $response['status'] = 'error';
    $response['message'] = 'Reset token is required';
    echo json_encode($response);
    exit();
}

if (empty($new_password) || empty($confirm_password)) {
    $response['status'] = 'error';
    $response['message'] = 'All password fields are required';
    echo json_encode($response);
    exit();
}

if ($new_password !== $confirm_password) {
    $response['status'] = 'error';
    $response['message'] = 'Passwords do not match';
    echo json_encode($response);
    exit();
}

// Validate password strength
if (strlen($new_password) < 6) {
    $response['status'] = 'error';
    $response['message'] = 'Password must be at least 6 characters long';
    echo json_encode($response);
    exit();
}

if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/", $new_password)) {
    $response['status'] = 'error';
    $response['message'] = 'Password must contain at least one lowercase letter, one uppercase letter, and one number';
    echo json_encode($response);
    exit();
}

try {
    $db = new db_connection();
    
    // Find user with valid token
    $sql = "SELECT customer_id, customer_email, customer_name, reset_token_expiry 
            FROM customer 
            WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $db->db_conn()->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid or expired reset token. Please request a new password reset.';
        echo json_encode($response);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password and clear reset token
    $update_sql = "UPDATE customer 
                   SET customer_pass = ?, reset_token = NULL, reset_token_expiry = NULL 
                   WHERE customer_id = ?";
    $update_stmt = $db->db_conn()->prepare($update_sql);
    $update_stmt->bind_param("si", $hashed_password, $user['customer_id']);
    
    if ($update_stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Your password has been successfully reset. You can now log in with your new password.';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to reset password. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Reset password error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred. Please try again later.';
}

echo json_encode($response);
?>
