<?php

require_once '../settings/db_class.php';

/**
 * Brand Class - Handles all brand-related database operations
 * Extends db_connection for database access
 * 
 * For VendorConnect Ghana: Brands represent vendor types or offerings
 * E.g., "Traditional Weddings", "Modern Weddings", "Destination Weddings"
 */
class Brand extends db_connection
{
    /**
     * Add a new brand
    * @param string $brand_name - Brand name
     * @param int $user_id - User ID who created the brand
     * @return int|false - Brand ID on success, false on failure
     */
    public function add_brand($brand_name, $user_id)
    {
        // Sanitize inputs
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);

        // Check if brand name already exists for this user
        if ($this->brand_exists($brand_name, null, $user_id)) {
            return false;
        }

        $sql = "INSERT INTO brands (brand_name, user_id) VALUES ('$brand_name', '$user_id')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }

    /**
     * Get all brands for a specific user
     * @param int $user_id - User ID
     * @return array|false - Array of brands or false on failure
     */
    public function get_all_brands($user_id)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT * FROM brands 
                WHERE user_id = '$user_id' 
                ORDER BY brand_name ASC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get a specific brand by ID (only if owned by user)
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array|false - Brand data or false on failure
     */
    public function get_brand($brand_id, $user_id)
    {
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT * FROM brands 
                WHERE brand_id = '$brand_id' AND user_id = '$user_id'";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Update a brand (only if owned by user)
     * @param int $brand_id - Brand ID
     * @param string $brand_name - New brand name
     * @param int $user_id - User ID
     * @return boolean - True on success, false on failure
     */
    public function update_brand($brand_id, $brand_name, $user_id)
    {
        // Sanitize inputs
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);

        // Check if brand exists and is owned by user
        if (!$this->get_brand($brand_id, $user_id)) {
            return false;
        }

        // Check if new name already exists (excluding current brand)
        if ($this->brand_exists($brand_name, $brand_id, $user_id)) {
            return false;
        }

        $sql = "UPDATE brands 
                SET brand_name = '$brand_name' 
                WHERE brand_id = '$brand_id' AND user_id = '$user_id'";
        
        return $this->db_write_query($sql);
    }

    /**
     * Delete a brand (only if owned by user)
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return boolean - True on success, false on failure
     */
    public function delete_brand($brand_id, $user_id)
    {
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);

        // Check if brand exists and is owned by user
        if (!$this->get_brand($brand_id, $user_id)) {
            return false;
        }

        // Check if brand is being used by any products
        $sql_check = "SELECT COUNT(*) as count FROM products WHERE product_brand = '$brand_id'";
        $result = $this->db_fetch_one($sql_check);
        
        if ($result && $result['count'] > 0) {
            return false; // Cannot delete brand that has products
        }

        $sql = "DELETE FROM brands WHERE brand_id = '$brand_id' AND user_id = '$user_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Check if brand name already exists for a user
     * @param string $brand_name - Brand name to check
     * @param int $exclude_id - Brand ID to exclude from check (for updates)
     * @param int $user_id - User ID
     * @return boolean - True if exists, false if not
     */
    private function brand_exists($brand_name, $exclude_id = null, $user_id)
    {
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT COUNT(*) as count 
                FROM brands 
                WHERE brand_name = '$brand_name' 
                AND user_id = '$user_id'";
        
        if ($exclude_id) {
            $exclude_id = mysqli_real_escape_string($this->db_conn(), $exclude_id);
            $sql .= " AND brand_id != '$exclude_id'";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] > 0;
    }

    /**
     * Search brands by name for a specific user
     * @param string $search_term - Search term
     * @param int $user_id - User ID
     * @return array|false - Array of matching brands or false on failure
     */
    public function search_brands($search_term, $user_id)
    {
        $search_term = mysqli_real_escape_string($this->db_conn(), $search_term);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT * FROM brands 
                WHERE brand_name LIKE '%$search_term%' 
                AND user_id = '$user_id' 
                ORDER BY brand_name ASC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get brand count for a specific user
     * @param int $user_id - User ID
     * @return int|false - Number of brands or false on failure
     */
    public function get_brand_count($user_id)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT COUNT(*) as count FROM brands WHERE user_id = '$user_id'";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['count'] : false;
    }

}
?>
