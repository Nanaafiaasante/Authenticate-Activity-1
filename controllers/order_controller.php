<?php
/**
 * Order Controller
 * Handles order operations by wrapping order_class methods
 * Acts as middleware between action scripts and the order model
 */

require_once(__DIR__ . '/../classes/order_class.php');

/**
 * Create a new order
 * 
 * @param int $customer_id - The customer ID
 * @param int $invoice_no - The invoice number
 * @param string $order_date - The order date
 * @param string $order_status - The order status (default: 'Pending')
 * @return int|false - Order ID on success, false on failure
 */
function create_order_ctr($customer_id, $invoice_no, $order_date, $order_status = 'Pending')
{
    $order = new Order();
    return $order->create_order($customer_id, $invoice_no, $order_date, $order_status);
}

/**
 * Add order details
 * 
 * @param int $order_id - The order ID
 * @param int $product_id - The product ID
 * @param int $quantity - The quantity
 * @return bool - True on success, false on failure
 */
function add_order_details_ctr($order_id, $product_id, $quantity)
{
    $order = new Order();
    return $order->add_order_details($order_id, $product_id, $quantity);
}

/**
 * Record payment
 * 
 * @param float $amount - The payment amount
 * @param int $customer_id - The customer ID
 * @param int $order_id - The order ID
 * @param string $currency - The currency (default: 'GHS')
 * @param string $payment_date - The payment date
 * @return int|false - Payment ID on success, false on failure
 */
function record_payment_ctr($amount, $customer_id, $order_id, $currency = 'GHS', $payment_date)
{
    $order = new Order();
    return $order->record_payment($amount, $customer_id, $order_id, $currency, $payment_date);
}

/**
 * Get user orders
 * 
 * @param int $customer_id - The customer ID
 * @return array|false - Array of orders, false on failure
 */
function get_user_orders_ctr($customer_id)
{
    $order = new Order();
    return $order->get_user_orders($customer_id);
}

/**
 * Get order details
 * 
 * @param int $order_id - The order ID
 * @return array|false - Array of order items, false on failure
 */
function get_order_details_ctr($order_id)
{
    $order = new Order();
    return $order->get_order_details($order_id);
}

/**
 * Get order by ID
 * 
 * @param int $order_id - The order ID
 * @return array|false - Order details, false on failure
 */
function get_order_by_id_ctr($order_id)
{
    $order = new Order();
    return $order->get_order_by_id($order_id);
}

/**
 * Get order by invoice number
 * 
 * @param int $invoice_no - The invoice number
 * @return array|false - Order details, false on failure
 */
function get_order_by_invoice_ctr($invoice_no)
{
    $order = new Order();
    return $order->get_order_by_invoice($invoice_no);
}

/**
 * Update order status
 * 
 * @param int $order_id - The order ID
 * @param string $status - The new status
 * @return bool - True on success, false on failure
 */
function update_order_status_ctr($order_id, $status)
{
    $order = new Order();
    return $order->update_order_status($order_id, $status);
}

/**
 * Get all orders (admin view)
 * 
 * @param string $status - Filter by status (optional)
 * @return array|false - Array of all orders, false on failure
 */
function get_all_orders_ctr($status = null)
{
    $order = new Order();
    return $order->get_all_orders($status);
}

/**
 * Get customer order statistics
 * 
 * @param int $customer_id - The customer ID
 * @return array|false - Order statistics, false on failure
 */
function get_customer_order_stats_ctr($customer_id)
{
    $order = new Order();
    return $order->get_customer_order_stats($customer_id);
}

/**
 * Generate unique invoice number
 * 
 * @return int - Unique invoice number
 */
function generate_invoice_number_ctr()
{
    $order = new Order();
    return $order->generate_invoice_number();
}

/**
 * Check if invoice exists
 * 
 * @param int $invoice_no - The invoice number
 * @return bool - True if exists, false otherwise
 */
function invoice_exists_ctr($invoice_no)
{
    $order = new Order();
    return $order->invoice_exists($invoice_no);
}

?>
