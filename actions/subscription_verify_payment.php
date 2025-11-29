<?php
header('Content-Type: application/json');

// Include required files
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';
require_once '../controllers/customer_controller.php';

error_log("=== SUBSCRIPTION PAYSTACK VERIFY ===");

// Get reference from GET or POST
$reference = '';
if (isset($_GET['reference'])) {
    $reference = trim($_GET['reference']);
} else {
    $input = json_decode(file_get_contents('php://input'), true);
    $reference = isset($input['reference']) ? trim($input['reference']) : '';
}

// Get tier from GET or POST
$tier = '';
if (isset($_GET['tier'])) {
    $tier = trim($_GET['tier']);
}

error_log("Verification request - Reference: $reference, Tier: $tier");

// Validate reference
if (empty($reference)) {
    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Payment reference is required'
    ]);
    exit();
}

try {
    // Verify transaction with Paystack
    $paystack_response = paystack_verify_transaction($reference);
    
    if (!$paystack_response) {
        throw new Exception("No response from Paystack verification API");
    }
    
    error_log("Paystack verification response: " . json_encode($paystack_response));
    
    // Check if verification was successful
    if (!isset($paystack_response['status']) || $paystack_response['status'] !== true) {
        $error_msg = $paystack_response['message'] ?? 'Payment verification failed';
        throw new Exception($error_msg);
    }
    
    $transaction_data = $paystack_response['data'];
    
    // Validate payment status
    if ($transaction_data['status'] !== 'success') {
        throw new Exception("Payment status is {$transaction_data['status']}. Payment not completed.");
    }
    
    // Convert amount from pesewas to GHS
    $paid_amount = pesewas_to_ghs($transaction_data['amount']);
    
    // Get metadata
    $metadata = $transaction_data['metadata'];
    $subscription_tier = $tier ?: ($metadata['subscription_tier'] ?? 'starter');
    
    // Check if user is logged in (existing user paying)
    if (isset($_SESSION['customer_id'])) {
        // Update existing user's subscription
        $customer_id = $_SESSION['customer_id'];
        
        // Calculate subscription dates (30 days for monthly)
        $subscription_start = date('Y-m-d');
        $subscription_end = date('Y-m-d', strtotime('+30 days'));
        
        require_once '../settings/db_class.php';
        $db = new db_connection();
        $conn = $db->db_conn();
        
        $sql = "UPDATE customer SET 
                subscription_status = 'active',
                subscription_tier = ?
                WHERE customer_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $subscription_tier, $customer_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update subscription status");
        }
        
        $stmt->close();
        $conn->close();
        
        // Update session
        $_SESSION['subscription_status'] = 'active';
        $_SESSION['subscription_tier'] = $subscription_tier;
        
        error_log("Subscription updated for customer ID: $customer_id");
        
    } else {
        // Get registration data from session storage (passed from frontend)
        $input = json_decode(file_get_contents('php://input'), true);
        $registration_data = $input['registration_data'] ?? null;
        
        if (!$registration_data) {
            throw new Exception("Registration data not found and user not logged in");
        }
        
        // Calculate subscription dates (30 days for monthly)
        $subscription_start = date('Y-m-d');
        $subscription_end = date('Y-m-d', strtotime('+30 days'));
        
        // Prepare customer data
        $customer_data = [
            'customer_name' => $registration_data['customer_name'],
            'customer_email' => $registration_data['customer_email'],
            'customer_pass' => $registration_data['customer_pass'],
            'customer_country' => $registration_data['customer_country'],
            'customer_city' => $registration_data['customer_city'],
            'customer_contact' => $registration_data['customer_contact'],
            'user_role' => $registration_data['user_role'],
            'subscription_tier' => $subscription_tier,
            'subscription_status' => 'active',
            'subscription_start_date' => $subscription_start,
            'subscription_end_date' => $subscription_end,
            'subscription_payment_ref' => $reference,
            'latitude' => $registration_data['latitude'] ?? null,
            'longitude' => $registration_data['longitude'] ?? null,
            'address' => $registration_data['address'] ?? null
        ];
        
        error_log("Creating customer account with subscription: " . json_encode($customer_data));
        
        // Register the customer
        $customer_id = register_customer_ctr($customer_data);
        
        if (!$customer_id) {
            throw new Exception("Failed to create customer account");
        }
        
        error_log("Customer registered successfully with ID: $customer_id");
    }
    
    // Return success response
    echo json_encode([
        'status' => 'success',
        'verified' => true,
        'message' => 'Subscription activated! Account created successfully.',
        'customer_id' => $customer_id,
        'subscription_tier' => $subscription_tier,
        'subscription_end_date' => date('F j, Y', strtotime($subscription_end)),
        'days_remaining' => 30,
        'payment_amount' => number_format($paid_amount, 2),
        'currency' => 'GHS',
        'payment_reference' => $reference
    ]);
    
} catch (Exception $e) {
    error_log("Subscription verification error: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Subscription verification failed: ' . $e->getMessage()
    ]);
}
?>
