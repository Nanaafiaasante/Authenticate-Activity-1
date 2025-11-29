<?php
/**
 * Order Class
 * Handles all order-related database operations
 * Extends db_connection class for database connectivity
 */

require_once(__DIR__ . '/../settings/db_class.php');

class Order extends db_connection
{
    /**
     * Create a new order
     * 
     * @param int $customer_id - The customer ID
     * @param int $invoice_no - The invoice/reference number
     * @param string $order_date - The order date (YYYY-MM-DD format)
     * @param string $order_status - The order status (default: 'Pending')
     * @return int|false - The order ID on success, false on failure
     */
    public function create_order($customer_id, $invoice_no, $order_date, $order_status = 'Pending')
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        $invoice_no = mysqli_real_escape_string($this->db_conn(), $invoice_no);
        $order_date = mysqli_real_escape_string($this->db_conn(), $order_date);
        $order_status = mysqli_real_escape_string($this->db_conn(), $order_status);

        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES ($customer_id, $invoice_no, '$order_date', '$order_status')";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }

    /**
     * Add order details (items in the order)
     * 
     * @param int $order_id - The order ID
     * @param int $product_id - The product ID
     * @param int $quantity - The quantity ordered
     * @param string $selected_items - JSON string of selected package items (optional)
     * @return bool - True on success, false on failure
     */
    public function add_order_details($order_id, $product_id, $quantity, $selected_items = null)
    {
        $order_id = mysqli_real_escape_string($this->db_conn(), $order_id);
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $quantity = mysqli_real_escape_string($this->db_conn(), $quantity);
        
        $selected_items_escaped = $selected_items ? "'" . mysqli_real_escape_string($this->db_conn(), $selected_items) . "'" : 'NULL';

        $sql = "INSERT INTO orderdetails (order_id, product_id, qty, selected_items) 
                VALUES ($order_id, $product_id, $quantity, $selected_items_escaped)";

        return $this->db_write_query($sql);
    }

    /**
     * Record a payment entry
     * 
     * @param float $amount - The payment amount
     * @param int $customer_id - The customer ID
     * @param int $order_id - The order ID
     * @param string $currency - The currency code (default: 'GHS')
     * @param string $payment_date - The payment date (YYYY-MM-DD format)
     * @param string $payment_method - Payment method (optional: 'paystack', 'cash', etc.)
     * @param string $transaction_ref - Transaction reference (optional)
     * @param string $authorization_code - Authorization code from gateway (optional)
     * @param string $payment_channel - Payment channel (optional: 'card', 'mobile_money', etc.)
     * @return int|false - The payment ID on success, false on failure
     */
    public function record_payment($amount, $customer_id, $order_id, $payment_date, $currency = 'GHS', $payment_method = null, $transaction_ref = null, $authorization_code = null, $payment_channel = null)
    {
        $conn = $this->db_conn();
        
        $amount = mysqli_real_escape_string($conn, $amount);
        $customer_id = mysqli_real_escape_string($conn, $customer_id);
        $order_id = mysqli_real_escape_string($conn, $order_id);
        $currency = mysqli_real_escape_string($conn, $currency);
        $payment_date = mysqli_real_escape_string($conn, $payment_date);

        // Build SQL with optional fields
        $columns = "(amt, customer_id, order_id, currency, payment_date";
        $values = "($amount, $customer_id, $order_id, '$currency', '$payment_date'";
        
        if ($payment_method !== null) {
            $payment_method = mysqli_real_escape_string($conn, $payment_method);
            $columns .= ", payment_method";
            $values .= ", '$payment_method'";
        }
        
        if ($transaction_ref !== null) {
            $transaction_ref = mysqli_real_escape_string($conn, $transaction_ref);
            $columns .= ", transaction_ref";
            $values .= ", '$transaction_ref'";
        }
        
        if ($authorization_code !== null) {
            $authorization_code = mysqli_real_escape_string($conn, $authorization_code);
            $columns .= ", authorization_code";
            $values .= ", '$authorization_code'";
        }
        
        if ($payment_channel !== null) {
            $payment_channel = mysqli_real_escape_string($conn, $payment_channel);
            $columns .= ", payment_channel";
            $values .= ", '$payment_channel'";
        }
        
        $columns .= ")";
        $values .= ")";

        $sql = "INSERT INTO payment $columns VALUES $values";

        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }

    /**
     * Get all orders for a specific customer
     * 
     * @param int $customer_id - The customer ID
     * @return array|false - Array of orders, false on failure
     */
    public function get_user_orders($customer_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);

        $sql = "SELECT o.*, p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = $customer_id
                ORDER BY o.order_date DESC, o.order_id DESC";

        return $this->db_fetch_all($sql);
    }

    /**
     * Get order details for a specific order
     * 
     * @param int $order_id - The order ID
     * @return array|false - Array of order items with product details, false on failure
     */
    public function get_order_details($order_id)
    {
        $order_id = mysqli_real_escape_string($this->db_conn(), $order_id);

        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image, p.product_cat, p.user_id,
                       c.customer_id as vendor_customer_id, c.customer_name as vendor_name,
                       c.customer_email as vendor_email, c.customer_contact as vendor_contact,
                       o.rating, o.review_comment, od.selected_items,
                       (od.qty * p.product_price) as item_total
                FROM orderdetails od
                INNER JOIN products p ON od.product_id = p.product_id
                INNER JOIN orders o ON od.order_id = o.order_id
                LEFT JOIN customer c ON p.user_id = c.customer_id
                WHERE od.order_id = $order_id";

        return $this->db_fetch_all($sql);
    }

    /**
     * Get a single order by order ID
     * 
     * @param int $order_id - The order ID
     * @return array|false - Order details, false on failure
     */
    public function get_order_by_id($order_id)
    {
        $order_id = mysqli_real_escape_string($this->db_conn(), $order_id);

        $sql = "SELECT o.*, c.customer_name, c.customer_email, 
                       c.customer_contact, c.customer_city, c.customer_country,
                       p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                INNER JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.order_id = $order_id";

        return $this->db_fetch_one($sql);
    }

    /**
     * Get order by invoice number
     * 
     * @param int $invoice_no - The invoice number
     * @return array|false - Order details, false on failure
     */
    public function get_order_by_invoice($invoice_no)
    {
        $invoice_no = mysqli_real_escape_string($this->db_conn(), $invoice_no);

        $sql = "SELECT o.*, c.customer_name, c.customer_email,
                       p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                INNER JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.invoice_no = $invoice_no";

        return $this->db_fetch_one($sql);
    }

    /**
     * Update order status
     * 
     * @param int $order_id - The order ID
     * @param string $status - The new status
     * @return bool - True on success, false on failure
     */
    public function update_order_status($order_id, $status)
    {
        $order_id = mysqli_real_escape_string($this->db_conn(), $order_id);
        $status = mysqli_real_escape_string($this->db_conn(), $status);

        $sql = "UPDATE orders 
                SET order_status = '$status' 
                WHERE order_id = $order_id";

        return $this->db_write_query($sql);
    }

    /**
     * Get all orders (for admin view)
     * 
     * @param string $status - Filter by status (optional)
     * @return array|false - Array of all orders, false on failure
     */
    public function get_all_orders($status = null)
    {
        $sql = "SELECT o.*, c.customer_name, c.customer_email,
                       p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                INNER JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id";

        if ($status) {
            $status = mysqli_real_escape_string($this->db_conn(), $status);
            $sql .= " WHERE o.order_status = '$status'";
        }

        $sql .= " ORDER BY o.order_date DESC, o.order_id DESC";

        return $this->db_fetch_all($sql);
    }

    /**
     * Get order statistics for a customer
     * 
     * @param int $customer_id - The customer ID
     * @return array|false - Order statistics, false on failure
     */
    public function get_customer_order_stats($customer_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);

        $sql = "SELECT 
                    COUNT(o.order_id) as total_orders,
                    SUM(p.amt) as total_spent,
                    COUNT(CASE WHEN o.order_status = 'Pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN o.order_status = 'Completed' THEN 1 END) as completed_orders
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = $customer_id";

        return $this->db_fetch_one($sql);
    }

    /**
     * Generate a unique invoice number
     * 
     * @return int - Unique invoice number
     */
    public function generate_invoice_number()
    {
        // Get the last invoice number
        $sql = "SELECT MAX(invoice_no) as last_invoice FROM orders";
        $result = $this->db_fetch_one($sql);
        
        if ($result && $result['last_invoice']) {
            return $result['last_invoice'] + 1;
        }
        
        // If no orders exist, start from 1000
        return 1000;
    }

    /**
     * Check if an invoice number exists
     * 
     * @param int $invoice_no - The invoice number to check
     * @return bool - True if exists, false otherwise
     */
    public function invoice_exists($invoice_no)
    {
        $invoice_no = mysqli_real_escape_string($this->db_conn(), $invoice_no);
        
        $sql = "SELECT COUNT(*) as count FROM orders WHERE invoice_no = $invoice_no";
        $result = $this->db_fetch_one($sql);
        
        return $result && $result['count'] > 0;
    }
}
?>
