<?php

require_once '../settings/db_class.php';

/**
 * Category Class - Handles all category-related database operations
 * Extends db_connection for database access
 */
class Category extends db_connection
{
    /**
     * Add a new category
     * @param string $cat_name - Category name
     * @param int $user_id - User ID who created the category
     * @return int|false - Category ID on success, false on failure
     */
    public function add_category($cat_name, $user_id)
    {
        // Check if category name already exists for this user
        if ($this->category_exists($cat_name, null, $user_id)) {
            return false;
        }

        $sql = "INSERT INTO categories (cat_name, user_id) VALUES ('$cat_name', '$user_id')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }

    /**
     * Get all categories for a specific user
     * @param int $user_id - User ID
     * @return array|false - Array of categories or false on failure
     */
    public function get_all_categories($user_id)
    {
        $sql = "SELECT * FROM categories WHERE user_id = '$user_id' ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get a specific category by ID (only if owned by user)
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID
     * @return array|false - Category data or false on failure
     */
    public function get_category($cat_id, $user_id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = '$cat_id' AND user_id = '$user_id'";
        return $this->db_fetch_one($sql);
    }

    /**
     * Update a category (only if owned by user)
     * @param int $cat_id - Category ID
     * @param string $cat_name - New category name
     * @param int $user_id - User ID
     * @return boolean - True on success, false on failure
     */
    public function update_category($cat_id, $cat_name, $user_id)
    {
        // Check if category exists and is owned by user
        if (!$this->get_category($cat_id, $user_id)) {
            return false;
        }

        // Check if new name already exists for this user (excluding current category)
        if ($this->category_exists($cat_name, $cat_id, $user_id)) {
            return false;
        }

        $sql = "UPDATE categories SET cat_name = '$cat_name' WHERE cat_id = '$cat_id' AND user_id = '$user_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Delete a category (only if owned by user)
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID
     * @return boolean - True on success, false on failure
     */
    public function delete_category($cat_id, $user_id)
    {
        // Check if category exists and is owned by user
        if (!$this->get_category($cat_id, $user_id)) {
            return false;
        }

        // Check if category is being used by any products
        $sql_check = "SELECT COUNT(*) as count FROM products WHERE product_cat = '$cat_id'";
        $result = $this->db_fetch_one($sql_check);
        
        if ($result && $result['count'] > 0) {
            return false; // Cannot delete category that has products
        }

        $sql = "DELETE FROM categories WHERE cat_id = '$cat_id' AND user_id = '$user_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Check if category name already exists for a user
     * @param string $cat_name - Category name to check
     * @param int $exclude_id - Category ID to exclude from check (for updates)
     * @param int $user_id - User ID
     * @return boolean - True if exists, false if not
     */
    private function category_exists($cat_name, $exclude_id = null, $user_id)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_name = '$cat_name' AND user_id = '$user_id'";
        
        if ($exclude_id) {
            $sql .= " AND cat_id != '$exclude_id'";
        }
        
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] > 0;
    }

    /**
     * Search categories by name for a specific user
     * @param string $search_term - Search term
     * @param int $user_id - User ID
     * @return array|false - Array of matching categories or false on failure
     */
    public function search_categories($search_term, $user_id)
    {
        $sql = "SELECT * FROM categories WHERE cat_name LIKE '%$search_term%' AND user_id = '$user_id' ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get category count for a specific user
     * @param int $user_id - User ID
     * @return int|false - Number of categories or false on failure
     */
    public function get_category_count($user_id)
    {
        $sql = "SELECT COUNT(*) as count FROM categories WHERE user_id = '$user_id'";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['count'] : false;
    }
}
?>
