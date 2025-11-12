<?php
/**
 * Cart Class
 * Handles all cart-related database operations
 * Extends db_connection class for database connectivity
 */

require_once(__DIR__ . '/../settings/db_class.php');

class Cart extends db_connection
{
    /**
     * Add a product to cart
     * If product already exists, update quantity instead of duplicating
     * 
     * @param int $product_id - The product ID
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address for guest carts
     * @param int $quantity - The quantity to add
     * @return bool - True on success, false on failure
     */
    public function add_to_cart($product_id, $customer_id, $ip_address, $quantity)
    {
        // Escape inputs to prevent SQL injection
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $customer_id = $customer_id ? mysqli_real_escape_string($this->db_conn(), $customer_id) : 'NULL';
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);
        $quantity = mysqli_real_escape_string($this->db_conn(), $quantity);

        // Check if product already exists in cart
        $existing = $this->check_product_exists($product_id, $customer_id, $ip_address);

        if ($existing) {
            // Update quantity instead of adding new entry
            $new_quantity = $existing['qty'] + $quantity;
            $sql = "UPDATE cart 
                    SET qty = $new_quantity 
                    WHERE p_id = $product_id 
                    AND ip_add = '$ip_address'";
            
            if ($customer_id !== 'NULL') {
                $sql .= " AND c_id = $customer_id";
            } else {
                $sql .= " AND (c_id IS NULL OR c_id = 0)";
            }
        } else {
            // Insert new cart item
            $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty) 
                    VALUES ($product_id, $customer_id, '$ip_address', $quantity)";
        }

        return $this->db_write_query($sql);
    }

    /**
     * Check if a product already exists in the cart
     * 
     * @param int $product_id - The product ID
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return array|false - Cart item if exists, false otherwise
     */
    public function check_product_exists($product_id, $customer_id, $ip_address)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "SELECT * FROM cart 
                WHERE p_id = $product_id 
                AND ip_add = '$ip_address'";

        if ($customer_id && $customer_id !== 'NULL') {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c_id = $customer_id";
        } else {
            $sql .= " AND (c_id IS NULL OR c_id = 0)";
        }

        return $this->db_fetch_one($sql);
    }

    /**
     * Update the quantity of a product in the cart
     * 
     * @param int $product_id - The product ID
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @param int $quantity - The new quantity
     * @return bool - True on success, false on failure
     */
    public function update_cart_quantity($product_id, $customer_id, $ip_address, $quantity)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);
        $quantity = mysqli_real_escape_string($this->db_conn(), $quantity);

        // If quantity is 0 or less, remove the item
        if ($quantity <= 0) {
            return $this->remove_from_cart($product_id, $customer_id, $ip_address);
        }

        $sql = "UPDATE cart 
                SET qty = $quantity 
                WHERE p_id = $product_id 
                AND ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c_id = $customer_id";
        } else {
            $sql .= " AND (c_id IS NULL OR c_id = 0)";
        }

        return $this->db_write_query($sql);
    }

    /**
     * Remove a product from the cart
     * 
     * @param int $product_id - The product ID
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return bool - True on success, false on failure
     */
    public function remove_from_cart($product_id, $customer_id, $ip_address)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "DELETE FROM cart 
                WHERE p_id = $product_id 
                AND ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c_id = $customer_id";
        } else {
            $sql .= " AND (c_id IS NULL OR c_id = 0)";
        }

        return $this->db_write_query($sql);
    }

    /**
     * Get all cart items for a user/guest with product details
     * 
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return array|false - Array of cart items with product details, false on failure
     */
    public function get_user_cart($customer_id, $ip_address)
    {
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, 
                       p.product_desc, p.product_cat, p.product_brand,
                       cat.cat_name, b.brand_name,
                       (c.qty * p.product_price) as subtotal
                FROM cart c
                INNER JOIN products p ON c.p_id = p.product_id
                LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE c.ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c.c_id = $customer_id";
        } else {
            $sql .= " AND (c.c_id IS NULL OR c.c_id = 0)";
        }

        $sql .= " ORDER BY c.p_id DESC";

        return $this->db_fetch_all($sql);
    }

    /**
     * Get cart item count for a user/guest
     * 
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return int - Total number of items in cart
     */
    public function get_cart_count($customer_id, $ip_address)
    {
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "SELECT SUM(qty) as total_items
                FROM cart 
                WHERE ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c_id = $customer_id";
        } else {
            $sql .= " AND (c_id IS NULL OR c_id = 0)";
        }

        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['total_items'] : 0;
    }

    /**
     * Empty the entire cart for a user/guest
     * 
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return bool - True on success, false on failure
     */
    public function empty_cart($customer_id, $ip_address)
    {
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "DELETE FROM cart 
                WHERE ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c_id = $customer_id";
        } else {
            $sql .= " AND (c_id IS NULL OR c_id = 0)";
        }

        return $this->db_write_query($sql);
    }

    /**
     * Get cart total amount for a user/guest
     * 
     * @param int $customer_id - The customer ID (null for guest)
     * @param string $ip_address - The IP address
     * @return float - Total cart amount
     */
    public function get_cart_total($customer_id, $ip_address)
    {
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);

        $sql = "SELECT SUM(c.qty * p.product_price) as total_amount
                FROM cart c
                INNER JOIN products p ON c.p_id = p.product_id
                WHERE c.ip_add = '$ip_address'";

        if ($customer_id) {
            $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
            $sql .= " AND c.c_id = $customer_id";
        } else {
            $sql .= " AND (c.c_id IS NULL OR c.c_id = 0)";
        }

        $result = $this->db_fetch_one($sql);
        return $result ? (float)$result['total_amount'] : 0.0;
    }

    /**
     * Transfer guest cart items to logged-in user
     * Useful when a guest logs in and we want to merge their cart
     * 
     * @param string $ip_address - The IP address
     * @param int $customer_id - The customer ID to transfer to
     * @return bool - True on success, false on failure
     */
    public function transfer_guest_cart($ip_address, $customer_id)
    {
        $ip_address = mysqli_real_escape_string($this->db_conn(), $ip_address);
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);

        $sql = "UPDATE cart 
                SET c_id = $customer_id 
                WHERE ip_add = '$ip_address' 
                AND (c_id IS NULL OR c_id = 0)";

        return $this->db_write_query($sql);
    }
}
?>
