<?php
header('Content-Type: application/json');

// Include required files
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';
require_once '../controllers/order_controller.php';
require_once '../controllers/cart_controller.php';

error_log("=== PAYSTACK VERIFY PAYMENT ===");

// Check if user is logged in
if (!check_login()) {
    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Please login to verify payment'
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$reference = isset($input['reference']) ? trim($input['reference']) : '';
$cart_items = isset($input['cart_items']) ? $input['cart_items'] : null;
$total_amount = isset($input['total_amount']) ? floatval($input['total_amount']) : 0;

error_log("Verification request - Reference: $reference, Amount: $total_amount");

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
        throw new Exception("No response from Paystack verification API. Please try again.");
    }
    
    // Check for timeout or connection errors
    if (isset($paystack_response['message']) && stripos($paystack_response['message'], 'timeout') !== false) {
        throw new Exception("Connection timeout. Please refresh the page to verify your payment.");
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
    
    // Validate amount (allow 1 pesewa tolerance for rounding)
    if (abs($paid_amount - $total_amount) > 0.01) {
        error_log("Amount mismatch - Expected: $total_amount, Paid: $paid_amount");
        throw new Exception("Payment amount mismatch");
    }
    
    // Get customer information
    $customer_id = get_user_id();
    $customer_email = $transaction_data['customer']['email'];
    
    // Get cart items if not provided
    if (!$cart_items) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $cart_items = get_user_cart_ctr($customer_id, $ip_address);
        if (!$cart_items || count($cart_items) === 0) {
            throw new Exception("Cart is empty");
        }
    }
    
    // Get customer details
    require_once '../controllers/customer_controller.php';
    $customer = get_customer_by_id_ctr($customer_id);
    $customer_name = $customer ? $customer['customer_name'] : 'Customer';
    
    // Generate invoice number (use last 6 digits of timestamp + 3 random digits for 9 digit number)
    $invoice_no = intval(substr(time(), -6) . rand(100, 999));
    $order_date = date('Y-m-d');
    $order_status = 'Paid';
    
    error_log("Creating order - Invoice: $invoice_no, Customer: $customer_id");
    
    // Start database transaction
    $conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }
    
    $conn->begin_transaction();
    
    try {
        // Create order
        $order_id = create_order_ctr($customer_id, $invoice_no, $order_date, $order_status);
        
        if (!$order_id) {
            throw new Exception("Failed to create order");
        }
        
        error_log("Order created with ID: $order_id");
        
        // Add order details (cart items)
        $item_count = 0;
        foreach ($cart_items as $item) {
            // Handle both product_id and p_id field names
            $product_id = isset($item['product_id']) ? $item['product_id'] : (isset($item['p_id']) ? $item['p_id'] : null);
            $qty = isset($item['qty']) ? $item['qty'] : 1;
            
            if (!$product_id) {
                error_log("Warning: Cart item missing product_id: " . json_encode($item));
                continue;
            }
            
            if (!add_order_details_ctr($order_id, $product_id, $qty)) {
                throw new Exception("Failed to add order item: Product ID $product_id");
            }
            
            $item_count++;
        }
        
        error_log("Added $item_count items to order");
        
        // Extract payment gateway details
        $payment_method = 'paystack';
        $transaction_ref = $transaction_data['reference'];
        $authorization_code = isset($transaction_data['authorization']['authorization_code']) ? 
            $transaction_data['authorization']['authorization_code'] : null;
        $payment_channel = isset($transaction_data['channel']) ? 
            ucfirst($transaction_data['channel']) : 'Online';
        
        // Record payment with all Paystack details
        $payment_id = record_payment_ctr(
            $paid_amount,
            $customer_id,
            $order_id,
            $order_date,
            'GHS',
            $payment_method,
            $transaction_ref,
            $authorization_code,
            $payment_channel
        );
        
        if (!$payment_id) {
            throw new Exception("Failed to record payment");
        }
        
        error_log("Payment recorded with ID: $payment_id");
        
        // Empty customer cart
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if (!empty_cart_ctr($customer_id, $ip_address)) {
            error_log("Warning: Failed to empty cart, but order was successful");
        }
        
        // Commit transaction
        $conn->commit();
        $conn->close();
        
        error_log("Order completed successfully - Order ID: $order_id, Payment ID: $payment_id");
        
        // Return success response
        echo json_encode([
            'status' => 'success',
            'verified' => true,
            'message' => 'Payment successful! Order confirmed.',
            'order_id' => $order_id,
            'invoice_no' => $invoice_no,
            'total_amount' => number_format($paid_amount, 2),
            'currency' => 'GHS',
            'order_date' => date('F j, Y', strtotime($order_date)),
            'customer_name' => $customer_name,
            'item_count' => $item_count,
            'payment_reference' => $transaction_ref,
            'payment_method' => $payment_channel,
            'customer_email' => $customer_email
        ]);
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $conn->close();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Payment verification error: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'verified' => false,
        'message' => 'Payment verification failed: ' . $e->getMessage()
    ]);
}
?>
