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

// Get email from request
$email = trim($_POST['email'] ?? '');

// Validate email
if (empty($email)) {
    $response['status'] = 'error';
    $response['message'] = 'Email is required';
    echo json_encode($response);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

try {
    $db = new db_connection();
    
    // Check if email exists
    $sql = "SELECT customer_id, customer_name, customer_email FROM customer WHERE customer_email = ?";
    $stmt = $db->db_conn()->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Don't reveal that email doesn't exist for security
        $response['status'] = 'success';
        $response['message'] = 'If your email is registered, you will receive a password reset link shortly.';
        echo json_encode($response);
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Generate reset token
    $token = bin2hex(random_bytes(32));
    $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $update_sql = "UPDATE customer SET reset_token = ?, reset_token_expiry = ? WHERE customer_email = ?";
    $update_stmt = $db->db_conn()->prepare($update_sql);
    $update_stmt->bind_param("sss", $token, $token_expiry, $email);
    
    if ($update_stmt->execute()) {
        // Return token directly for immediate reset (no email)
        $response['status'] = 'success';
        $response['message'] = 'Email verified. Redirecting to password reset...';
        $response['reset_token'] = $token;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to generate reset token. Please try again.';
    }
    
} catch (Exception $e) {
    error_log("Forgot password error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred. Please try again later.';
}

echo json_encode($response);
?>
