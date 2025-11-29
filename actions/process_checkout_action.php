<?php
/**
 * Process Checkout Action
 * Handles the complete checkout process including:
 * - Creating an order
 * - Adding order details
 * - Recording payment
 * - Emptying the cart
 */

// Start output buffering to catch any accidental output
ob_start();

session_start();

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json; charset=utf-8');

// Include required controllers
require_once(__DIR__ . '/../controllers/cart_controller.php');
require_once(__DIR__ . '/../controllers/order_controller.php');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit();
}

// Check if user is logged in (guests cannot checkout)
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to checkout. Please login or register.'
    ]);
    exit();
}

$customer_id = intval($_SESSION['customer_id']);
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Get cart items
    $cart_items = get_user_cart_ctr($customer_id, $ip_address);

    // Check if cart is empty
    if (!$cart_items || count($cart_items) == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Your cart is empty'
        ]);
        exit();
    }

    // Calculate total amount
    $total_amount = get_cart_total_ctr($customer_id, $ip_address);

    // Generate unique invoice number
    $invoice_no = generate_invoice_number_ctr();

    // Ensure invoice number is unique
    $max_attempts = 10;
    $attempts = 0;
    while (invoice_exists_ctr($invoice_no) && $attempts < $max_attempts) {
        $invoice_no++;
        $attempts++;
    }

    if (invoice_exists_ctr($invoice_no)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to generate unique invoice number. Please try again.'
        ]);
        exit();
    }

    // Get current date
    $order_date = date('Y-m-d');
    $payment_date = date('Y-m-d');

    // Create the order
    $order_id = create_order_ctr($customer_id, $invoice_no, $order_date, 'Pending');

    if (!$order_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create order. Please try again.'
        ]);
        exit();
    }

    // Add order details for each cart item
    $order_details_success = true;
    foreach ($cart_items as $item) {
        // Get selected_items from cart if available
        $selected_items = isset($item['selected_items']) ? $item['selected_items'] : null;
        
        $result = add_order_details_ctr($order_id, $item['p_id'], $item['qty'], $selected_items);
        if (!$result) {
            $order_details_success = false;
            break;
        }
    }

    if (!$order_details_success) {
        // Rollback: You might want to delete the order here
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add order details. Please try again.'
        ]);
        exit();
    }

    // Record payment
    $currency = 'GHS'; // Ghana Cedis
    $payment_id = record_payment_ctr($total_amount, $customer_id, $order_id, $currency, $payment_date);

    if (!$payment_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to record payment. Please contact support.'
        ]);
        exit();
    }

    // Empty the cart
    $cart_emptied = empty_cart_ctr($customer_id, $ip_address);

    if (!$cart_emptied) {
        // Even if cart emptying fails, the order was successful
        // Log this for admin review
        error_log("Cart not emptied after successful order. Customer ID: $customer_id, Order ID: $order_id");
    }

    // Success! Return order details
    $response = json_encode([
        'status' => 'success',
        'message' => 'Order placed successfully!',
        'order_id' => $order_id,
        'invoice_no' => $invoice_no,
        'total_amount' => number_format($total_amount, 2),
        'currency' => $currency,
        'order_date' => date('F j, Y', strtotime($order_date)),
        'items_count' => count($cart_items)
    ]);
    
    // Flush output buffer and send response
    ob_end_clean();
    echo $response;
    exit();

} catch (Exception $e) {
    // Log the error
    error_log("Checkout error: " . $e->getMessage());
    
    $response = json_encode([
        'status' => 'error',
        'message' => 'An error occurred during checkout: ' . $e->getMessage()
    ]);
    
    // Flush output buffer and send response
    ob_end_clean();
    echo $response;
    exit();
}
?>
