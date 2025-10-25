<?php

require_once '../settings/db_class.php';

/**
 * Product Class - Handles all product-related database operations
 * Extends db_connection for database access
 * 
 * For VendorConnect Ghana: Products represent wedding planner service profiles
 * Each product is a planner's service offering with portfolio, pricing, and details
 */
class Product extends db_connection
{
    /**
     * Add a new product
     * @param array $data - Product data array
     * @return int|false - Product ID on success, false on failure
     */
    public function add_product($data)
    {
        $conn = $this->db_conn();
        
        // Sanitize inputs
        $product_cat = mysqli_real_escape_string($conn, $data['product_cat']);
        $product_brand = mysqli_real_escape_string($conn, $data['product_brand']);
        $product_title = mysqli_real_escape_string($conn, $data['product_title']);
        $product_price = mysqli_real_escape_string($conn, $data['product_price']);
        $product_desc = mysqli_real_escape_string($conn, $data['product_desc']);
        $product_image = mysqli_real_escape_string($conn, $data['product_image']);
        $product_keywords = mysqli_real_escape_string($conn, $data['product_keywords']);
        $user_id = mysqli_real_escape_string($conn, $data['user_id']);

        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, 
                product_desc, product_image, product_keywords, user_id) 
                VALUES ('$product_cat', '$product_brand', '$product_title', '$product_price', 
                '$product_desc', '$product_image', '$product_keywords', '$user_id')";
        
        if ($this->db_write_query($sql)) {
            return $this->last_insert_id();
        }
        
        return false;
    }

    /**
     * Get all products for a specific user
     * @param int $user_id - User ID
     * @return array|false - Array of products with category and brand info or false on failure
     */
    public function get_all_products($user_id)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.user_id = '$user_id' 
                ORDER BY p.created_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get products by category
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID
     * @return array|false - Array of products or false on failure
     */
    public function get_products_by_category($cat_id, $user_id)
    {
        $cat_id = mysqli_real_escape_string($this->db_conn(), $cat_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = '$cat_id' AND p.user_id = '$user_id' 
                ORDER BY p.created_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get products by brand
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return array|false - Array of products or false on failure
     */
    public function get_products_by_brand($brand_id, $user_id)
    {
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = '$brand_id' AND p.user_id = '$user_id' 
                ORDER BY p.created_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get a specific product by ID (only if owned by user)
     * @param int $product_id - Product ID
     * @param int $user_id - User ID
     * @return array|false - Product data with category and brand info or false on failure
     */
    public function get_product($product_id, $user_id)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = '$product_id' AND p.user_id = '$user_id'";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Update a product (only if owned by user)
     * @param array $data - Product data array including product_id
     * @return boolean - True on success, false on failure
     */
    public function update_product($data)
    {
        $conn = $this->db_conn();
        
        // Sanitize inputs
        $product_id = mysqli_real_escape_string($conn, $data['product_id']);
        $product_cat = mysqli_real_escape_string($conn, $data['product_cat']);
        $product_brand = mysqli_real_escape_string($conn, $data['product_brand']);
        $product_title = mysqli_real_escape_string($conn, $data['product_title']);
        $product_price = mysqli_real_escape_string($conn, $data['product_price']);
        $product_desc = mysqli_real_escape_string($conn, $data['product_desc']);
        $product_keywords = mysqli_real_escape_string($conn, $data['product_keywords']);
        $user_id = mysqli_real_escape_string($conn, $data['user_id']);

        // Check if product exists and is owned by user
        if (!$this->get_product($product_id, $user_id)) {
            return false;
        }

        // Build update query (image is optional in update)
        if (isset($data['product_image']) && !empty($data['product_image'])) {
            $product_image = mysqli_real_escape_string($conn, $data['product_image']);
            $sql = "UPDATE products 
                    SET product_cat = '$product_cat', 
                        product_brand = '$product_brand', 
                        product_title = '$product_title', 
                        product_price = '$product_price', 
                        product_desc = '$product_desc', 
                        product_image = '$product_image', 
                        product_keywords = '$product_keywords' 
                    WHERE product_id = '$product_id' AND user_id = '$user_id'";
        } else {
            $sql = "UPDATE products 
                    SET product_cat = '$product_cat', 
                        product_brand = '$product_brand', 
                        product_title = '$product_title', 
                        product_price = '$product_price', 
                        product_desc = '$product_desc', 
                        product_keywords = '$product_keywords' 
                    WHERE product_id = '$product_id' AND user_id = '$user_id'";
        }
        
        return $this->db_write_query($sql);
    }

    /**
     * Delete a product (only if owned by user)
     * @param int $product_id - Product ID
     * @param int $user_id - User ID
     * @return boolean - True on success, false on failure
     */
    public function delete_product($product_id, $user_id)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);

        // Check if product exists and is owned by user
        $product = $this->get_product($product_id, $user_id);
        if (!$product) {
            return false;
        }

        // Delete product image if exists
        if (!empty($product['product_image']) && file_exists($product['product_image'])) {
            unlink($product['product_image']);
        }

        $sql = "DELETE FROM products WHERE product_id = '$product_id' AND user_id = '$user_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Search products by title or keywords for a specific user (admin)
     * @param string $search_term - Search term
     * @param int $user_id - User ID
     * @return array|false - Array of matching products or false on failure
     */
    public function search_user_products($search_term, $user_id)
    {
        $search_term = mysqli_real_escape_string($this->db_conn(), $search_term);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                WHERE (p.product_title LIKE '%$search_term%' OR p.product_keywords LIKE '%$search_term%') 
                AND p.user_id = '$user_id' 
                ORDER BY p.created_at DESC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get product count for a specific user
     * @param int $user_id - User ID
     * @return int|false - Number of products or false on failure
     */
    public function get_product_count($user_id)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT COUNT(*) as count FROM products WHERE user_id = '$user_id'";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['count'] : false;
    }

    /**
     * View all products (customer-facing)
     * @param int $limit - Optional limit for pagination
     * @param int $offset - Optional offset for pagination
     * @return array|false - Array of all products with vendor info or false on failure
     */
    public function view_all_products($limit = null, $offset = null)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                ORDER BY p.created_at DESC";
        
        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * View products for a specific user (admin dashboard)
     * @param int $user_id
     * @param int|null $limit
     * @param int|null $offset
     * @return array|false
     */
    public function view_user_products($user_id, $limit = null, $offset = null)
    {
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);

        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                WHERE p.user_id = '$user_id'
                ORDER BY p.created_at DESC";

        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db_fetch_all($sql);
    }

    /**
     * Search products by title, description or keywords (customer-facing)
     * @param string $query - Search query
     * @param int $limit - Optional limit for pagination
     * @param int $offset - Optional offset for pagination
     * @return array|false - Array of matching products or false on failure
     */
    public function search_products($query, $limit = null, $offset = null)
    {
        $query = mysqli_real_escape_string($this->db_conn(), $query);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                WHERE p.product_title LIKE '%$query%' 
                   OR p.product_desc LIKE '%$query%' 
                   OR p.product_keywords LIKE '%$query%'
                ORDER BY p.created_at DESC";
        
        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Filter products by category (customer-facing)
     * @param int $cat_id - Category ID
     * @param int $limit - Optional limit for pagination
     * @param int $offset - Optional offset for pagination
     * @return array|false - Array of products in category or false on failure
     */
    public function filter_products_by_category($cat_id, $limit = null, $offset = null)
    {
        $cat_id = mysqli_real_escape_string($this->db_conn(), $cat_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                WHERE p.product_cat = '$cat_id'
                ORDER BY p.created_at DESC";
        
        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Filter products by brand (customer-facing)
     * @param int $brand_id - Brand ID
     * @param int $limit - Optional limit for pagination
     * @param int $offset - Optional offset for pagination
     * @return array|false - Array of products with brand or false on failure
     */
    public function filter_products_by_brand($brand_id, $limit = null, $offset = null)
    {
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                WHERE p.product_brand = '$brand_id'
                ORDER BY p.created_at DESC";
        
        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * View single product details (customer-facing)
     * @param int $product_id - Product ID
     * @return array|false - Product details with vendor info or false on failure
     */
    public function view_single_product($product_id)
    {
        $product_id = mysqli_real_escape_string($this->db_conn(), $product_id);
        
        $sql = "SELECT p.*, c.cat_name, b.brand_name, 
                       cu.customer_name as vendor_name,
                       cu.customer_email as vendor_email,
                       cu.customer_contact as vendor_contact,
                       cu.customer_city as vendor_city
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id
                WHERE p.product_id = '$product_id'";
        
        return $this->db_fetch_one($sql);
    }

    /**
     * Filter products by multiple criteria (composite search)
     * @param array $filters - Array of filters (category, brand, min_price, max_price, search)
     * @param int $limit - Optional limit for pagination
     * @param int $offset - Optional offset for pagination
     * @return array|false - Array of filtered products or false on failure
     */
    public function filter_products_composite($filters, $limit = null, $offset = null)
    {
        $where_clauses = [];
        
        // Category filter
        if (!empty($filters['category'])) {
            $cat_id = mysqli_real_escape_string($this->db_conn(), $filters['category']);
            $where_clauses[] = "p.product_cat = '$cat_id'";
        }
        
        // Brand filter
        if (!empty($filters['brand'])) {
            $brand_id = mysqli_real_escape_string($this->db_conn(), $filters['brand']);
            $where_clauses[] = "p.product_brand = '$brand_id'";
        }
        
        // Price range filter
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $min_price = mysqli_real_escape_string($this->db_conn(), $filters['min_price']);
            $where_clauses[] = "p.product_price >= '$min_price'";
        }
        
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $max_price = mysqli_real_escape_string($this->db_conn(), $filters['max_price']);
            $where_clauses[] = "p.product_price <= '$max_price'";
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $search = mysqli_real_escape_string($this->db_conn(), $filters['search']);
            $where_clauses[] = "(p.product_title LIKE '%$search%' OR p.product_desc LIKE '%$search%' OR p.product_keywords LIKE '%$search%')";
        }
        
        // Build SQL
        $sql = "SELECT p.*, c.cat_name, b.brand_name, cu.customer_name as vendor_name
                FROM products p 
                INNER JOIN categories c ON p.product_cat = c.cat_id 
                INNER JOIN brands b ON p.product_brand = b.brand_id 
                INNER JOIN customer cu ON p.user_id = cu.customer_id";
        
        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        // Handle sorting
        $order_by = "p.created_at DESC"; // Default sort
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low':
                    $order_by = "p.product_price ASC";
                    break;
                case 'price_high':
                    $order_by = "p.product_price DESC";
                    break;
                case 'name':
                    $order_by = "p.product_title ASC";
                    break;
                case 'newest':
                default:
                    $order_by = "p.created_at DESC";
                    break;
            }
        }
        
        $sql .= " ORDER BY $order_by";
        
        if ($limit !== null && $offset !== null) {
            $limit = mysqli_real_escape_string($this->db_conn(), $limit);
            $offset = mysqli_real_escape_string($this->db_conn(), $offset);
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get total product count for pagination
     * @return int - Total number of products
     */
    public function get_total_product_count()
    {
        $sql = "SELECT COUNT(*) as count FROM products";
        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get count for filtered products (for pagination)
     * @param array $filters - Array of filters
     * @return int - Count of filtered products
     */
    public function get_filtered_product_count($filters)
    {
        $where_clauses = [];
        
        if (!empty($filters['category'])) {
            $cat_id = mysqli_real_escape_string($this->db_conn(), $filters['category']);
            $where_clauses[] = "p.product_cat = '$cat_id'";
        }
        
        if (!empty($filters['brand'])) {
            $brand_id = mysqli_real_escape_string($this->db_conn(), $filters['brand']);
            $where_clauses[] = "p.product_brand = '$brand_id'";
        }
        
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $min_price = mysqli_real_escape_string($this->db_conn(), $filters['min_price']);
            $where_clauses[] = "p.product_price >= '$min_price'";
        }
        
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $max_price = mysqli_real_escape_string($this->db_conn(), $filters['max_price']);
            $where_clauses[] = "p.product_price <= '$max_price'";
        }
        
        if (!empty($filters['search'])) {
            $search = mysqli_real_escape_string($this->db_conn(), $filters['search']);
            $where_clauses[] = "(p.product_title LIKE '%$search%' OR p.product_desc LIKE '%$search%' OR p.product_keywords LIKE '%$search%')";
        }
        
        $sql = "SELECT COUNT(*) as count FROM products p";
        
        if (!empty($where_clauses)) {
            $sql .= " WHERE " . implode(' AND ', $where_clauses);
        }
        
        $result = $this->db_fetch_one($sql);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get all categories (for filter dropdown)
     * @return array|false - Array of all categories
     */
    public function get_all_categories()
    {
        $sql = "SELECT DISTINCT c.* FROM categories c
                INNER JOIN products p ON c.cat_id = p.product_cat
                ORDER BY c.cat_name ASC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Get all brands (for filter dropdown)
     * @return array|false - Array of all brands
     */
    public function get_all_brands()
    {
        $sql = "SELECT DISTINCT b.* FROM brands b
                INNER JOIN products p ON b.brand_id = p.product_brand
                ORDER BY b.brand_name ASC";
        
        return $this->db_fetch_all($sql);
    }

    /**
     * Verify that a category exists and belongs to the user
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID
     * @return boolean - True if category belongs to user, false otherwise
     */
    public function category_belongs_to_user($cat_id, $user_id)
    {
        $cat_id = mysqli_real_escape_string($this->db_conn(), $cat_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT COUNT(*) as count FROM categories WHERE cat_id = '$cat_id' AND user_id = '$user_id'";
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] > 0;
    }

    /**
     * Verify that a brand exists and belongs to the user
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID
     * @return boolean - True if brand belongs to user, false otherwise
     */
    public function brand_belongs_to_user($brand_id, $user_id)
    {
        $brand_id = mysqli_real_escape_string($this->db_conn(), $brand_id);
        $user_id = mysqli_real_escape_string($this->db_conn(), $user_id);
        
        $sql = "SELECT COUNT(*) as count FROM brands WHERE brand_id = '$brand_id' AND user_id = '$user_id'";
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] > 0;
    }
}
?>
