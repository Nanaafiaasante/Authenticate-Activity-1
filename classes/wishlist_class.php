<?php
/**
 * Wishlist Class
 * Database operations for wishlist management
 */

require_once(__DIR__ . '/../settings/db_class.php');

class Wishlist extends db_connection
{
    /**
     * Add product to wishlist
     */
    public function add_to_wishlist($customer_id, $product_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        
        // Check if already in wishlist
        $check = $this->check_in_wishlist($customer_id, $product_id);
        if ($check) {
            return true; // Already in wishlist
        }
        
        $sql = "INSERT INTO wishlist (customer_id, product_id) 
                VALUES ($customer_id, $product_id)";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Remove product from wishlist
     */
    public function remove_from_wishlist($customer_id, $product_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        
        $sql = "DELETE FROM wishlist 
                WHERE customer_id = $customer_id 
                AND product_id = $product_id";
        
        return $this->db_write_query($sql);
    }
    
    /**
     * Get all wishlist items with product details
     */
    public function get_wishlist_items($customer_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        
        $sql = "SELECT w.*, 
                       p.product_title, p.product_price, p.product_image, 
                       p.product_desc, p.product_cat, p.product_brand,
                       cat.cat_name, b.brand_name,
                       u.customer_name as vendor_name, u.vendor_name as vendor_business_name,
                       u.customer_city as vendor_city
                FROM wishlist w
                INNER JOIN products p ON w.product_id = p.product_id
                LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                LEFT JOIN customer u ON p.user_id = u.customer_id
                WHERE w.customer_id = $customer_id
                ORDER BY w.added_date DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    /**
     * Get wishlist item count
     */
    public function get_wishlist_count($customer_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        
        $sql = "SELECT COUNT(*) as count 
                FROM wishlist 
                WHERE customer_id = $customer_id";
        
        $result = $this->db_fetch_one($sql);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Check if product is in wishlist
     */
    public function check_in_wishlist($customer_id, $product_id)
    {
        $customer_id = mysqli_real_escape_string($this->db_conn(), $customer_id);
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        
        $sql = "SELECT wishlist_id 
                FROM wishlist 
                WHERE customer_id = $customer_id 
                AND product_id = $product_id";
        
        $result = $this->db_fetch_one($sql);
        return $result ? true : false;
    }
}
?>
