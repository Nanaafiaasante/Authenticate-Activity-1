<?php

require_once '../classes/product_class.php';

/**
 * Product Controller - Handles product business logic
 * Creates instances of product class and runs methods
 * 
 * For VendorConnect Ghana: Manages wedding planner service profiles
 */
class ProductController
{
    private $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    /**
     * Add product controller method
     * @param array $kwargs - Array containing product data
     * @return array - Response array with status and message
     */
    public function add_product_ctr($kwargs)
    {
        // Extract and validate required fields
        $product_cat = $kwargs['product_cat'] ?? null;
        $product_brand = $kwargs['product_brand'] ?? null;
        $product_title = trim($kwargs['product_title'] ?? '');
        $product_price = $kwargs['product_price'] ?? null;
        $product_desc = trim($kwargs['product_desc'] ?? '');
        $product_image = $kwargs['product_image'] ?? '';
        $product_keywords = trim($kwargs['product_keywords'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate inputs
        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        if (!$product_cat || !is_numeric($product_cat)) {
            return [
                'status' => 'error',
                'message' => 'Valid category is required'
            ];
        }

        if (!$product_brand || !is_numeric($product_brand)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand is required'
            ];
        }

        if (empty($product_title)) {
            return [
                'status' => 'error',
                'message' => 'Service title is required'
            ];
        }

        if (strlen($product_title) > 200) {
            return [
                'status' => 'error',
                'message' => 'Service title must be less than 200 characters'
            ];
        }

        if ($product_price === null || $product_price === '') {
            return [
                'status' => 'error',
                'message' => 'Service price is required'
            ];
        }

        if (!is_numeric($product_price) || $product_price < 0) {
            return [
                'status' => 'error',
                'message' => 'Valid price is required (must be a positive number)'
            ];
        }

        if (strlen($product_desc) > 500) {
            return [
                'status' => 'error',
                'message' => 'Description must be less than 500 characters'
            ];
        }

        // Verify category and brand belong to user
        if (!$this->product->category_belongs_to_user($product_cat, $user_id)) {
            return [
                'status' => 'error',
                'message' => 'Invalid category selected'
            ];
        }

        if (!$this->product->brand_belongs_to_user($product_brand, $user_id)) {
            return [
                'status' => 'error',
                'message' => 'Invalid brand selected'
            ];
        }

        // Prepare data array
        $data = [
            'product_cat' => $product_cat,
            'product_brand' => $product_brand,
            'product_title' => $product_title,
            'product_price' => $product_price,
            'product_desc' => $product_desc,
            'product_image' => $product_image,
            'product_keywords' => $product_keywords,
            'user_id' => $user_id
        ];

        // Call product class method
        $result = $this->product->add_product($data);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Service added successfully',
                'product_id' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to add service'
            ];
        }
    }

    /**
     * Get all products controller method
     * @param array $kwargs - Array containing user ID
     * @return array - Response array with status and data
     */
    public function get_all_products_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $products = $this->product->get_all_products($user_id);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch services'
            ];
        }
    }

    /**
     * Get products by category controller method
     * @param array $kwargs - Array containing category ID and user ID
     * @return array - Response array with status and data
     */
    public function get_products_by_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'] ?? null;
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$cat_id || !is_numeric($cat_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid category ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $products = $this->product->get_products_by_category($cat_id, $user_id);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch services'
            ];
        }
    }

    /**
     * Get single product controller method
     * @param array $kwargs - Array containing product ID and user ID
     * @return array - Response array with status and data
     */
    public function get_product_ctr($kwargs)
    {
        $product_id = $kwargs['product_id'] ?? null;
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$product_id || !is_numeric($product_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid service ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $product = $this->product->get_product($product_id, $user_id);
        
        if ($product) {
            return [
                'status' => 'success',
                'data' => $product
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Service not found'
            ];
        }
    }

    /**
     * Update product controller method
     * @param array $kwargs - Array containing product data
     * @return array - Response array with status and message
     */
    public function update_product_ctr($kwargs)
    {
        // Extract and validate required fields
        $product_id = $kwargs['product_id'] ?? null;
        $product_cat = $kwargs['product_cat'] ?? null;
        $product_brand = $kwargs['product_brand'] ?? null;
        $product_title = trim($kwargs['product_title'] ?? '');
        $product_price = $kwargs['product_price'] ?? null;
        $product_desc = trim($kwargs['product_desc'] ?? '');
        $product_image = $kwargs['product_image'] ?? null;
        $product_keywords = trim($kwargs['product_keywords'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate inputs
        if (!$product_id || !is_numeric($product_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid service ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        if (!$product_cat || !is_numeric($product_cat)) {
            return [
                'status' => 'error',
                'message' => 'Valid category is required'
            ];
        }

        if (!$product_brand || !is_numeric($product_brand)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand is required'
            ];
        }

        if (empty($product_title)) {
            return [
                'status' => 'error',
                'message' => 'Service title is required'
            ];
        }

        if (strlen($product_title) > 200) {
            return [
                'status' => 'error',
                'message' => 'Service title must be less than 200 characters'
            ];
        }

        if ($product_price === null || $product_price === '') {
            return [
                'status' => 'error',
                'message' => 'Service price is required'
            ];
        }

        if (!is_numeric($product_price) || $product_price < 0) {
            return [
                'status' => 'error',
                'message' => 'Valid price is required (must be a positive number)'
            ];
        }

        if (strlen($product_desc) > 500) {
            return [
                'status' => 'error',
                'message' => 'Description must be less than 500 characters'
            ];
        }

        // Verify category and brand belong to user
        if (!$this->product->category_belongs_to_user($product_cat, $user_id)) {
            return [
                'status' => 'error',
                'message' => 'Invalid category selected'
            ];
        }

        if (!$this->product->brand_belongs_to_user($product_brand, $user_id)) {
            return [
                'status' => 'error',
                'message' => 'Invalid brand selected'
            ];
        }

        // Prepare data array
        $data = [
            'product_id' => $product_id,
            'product_cat' => $product_cat,
            'product_brand' => $product_brand,
            'product_title' => $product_title,
            'product_price' => $product_price,
            'product_desc' => $product_desc,
            'product_keywords' => $product_keywords,
            'user_id' => $user_id
        ];

        // Add image if provided
        if ($product_image !== null && !empty($product_image)) {
            $data['product_image'] = $product_image;
        }

        // Call product class method
        $result = $this->product->update_product($data);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Service updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update service or service not found'
            ];
        }
    }

    /**
     * Delete product controller method
     * @param array $kwargs - Array containing product ID and user ID
     * @return array - Response array with status and message
     */
    public function delete_product_ctr($kwargs)
    {
        $product_id = $kwargs['product_id'] ?? null;
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$product_id || !is_numeric($product_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid service ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        // Call product class method
        $result = $this->product->delete_product($product_id, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Service deleted successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Service not found or failed to delete'
            ];
        }
    }

    /**
     * Search products controller method (admin - user's own products)
     * @param array $kwargs - Array containing search term and user ID
     * @return array - Response array with status and data
     */
    public function search_products_ctr($kwargs)
    {
        $search_term = trim($kwargs['search_term'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        if (empty($search_term)) {
            return [
                'status' => 'error',
                'message' => 'Search term is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $products = $this->product->search_user_products($search_term, $user_id);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to search services'
            ];
        }
    }

    /**
     * View all products controller (customer-facing)
     * @param array $kwargs - Array containing optional limit, offset, and user location for pagination and distance sorting
     * @return array - Response array with status and data
     */
    public function view_all_products_ctr($kwargs)
    {
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;
        $user_lat = $kwargs['user_latitude'] ?? null;
        $user_lon = $kwargs['user_longitude'] ?? null;
        
        $products = $this->product->view_all_products($limit, $offset, $user_lat, $user_lon);
        $total_count = $this->product->get_total_product_count();
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products),
                'total' => $total_count,
                'sorted_by_distance' => ($user_lat !== null && $user_lon !== null)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch products'
            ];
        }
    }

    /**
     * View products created by a specific user (admin dashboard)
     * @param array $kwargs - ['user_id' => int, 'limit' => int|null, 'offset' => int|null]
     * @return array
     */
    public function view_user_products_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? null;
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;

        if (!$user_id) {
            return [
                'status' => 'error',
                'message' => 'User ID is required'
            ];
        }

        $products = $this->product->view_user_products($user_id, $limit, $offset);
        $total_count = $this->product->get_product_count($user_id);

        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => is_array($products) ? count($products) : 0,
                'total' => (int)$total_count
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch user products'
            ];
        }
    }

    /**
     * Search all products controller (customer-facing)
     * @param array $kwargs - Array containing search query and pagination params
     * @return array - Response array with status and data
     */
    public function search_all_products_ctr($kwargs)
    {
        $query = trim($kwargs['query'] ?? '');
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;
        
        if (empty($query)) {
            return [
                'status' => 'error',
                'message' => 'Search query is required'
            ];
        }
        
        $products = $this->product->search_products($query, $limit, $offset);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products),
                'query' => $query
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to search products'
            ];
        }
    }

    /**
     * Filter products by category controller (customer-facing)
     * @param array $kwargs - Array containing category ID and pagination params
     * @return array - Response array with status and data
     */
    public function filter_by_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'] ?? null;
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;
        
        if (!$cat_id || !is_numeric($cat_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid category ID is required'
            ];
        }
        
        $products = $this->product->filter_products_by_category($cat_id, $limit, $offset);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to filter products'
            ];
        }
    }

    /**
     * Filter products by brand controller (customer-facing)
     * @param array $kwargs - Array containing brand ID and pagination params
     * @return array - Response array with status and data
     */
    public function filter_by_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? null;
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;
        
        if (!$brand_id || !is_numeric($brand_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand ID is required'
            ];
        }
        
        $products = $this->product->filter_products_by_brand($brand_id, $limit, $offset);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to filter products'
            ];
        }
    }

    /**
     * View single product controller (customer-facing)
     * @param array $kwargs - Array containing product ID
     * @return array - Response array with status and data
     */
    public function view_single_product_ctr($kwargs)
    {
        $product_id = $kwargs['product_id'] ?? null;
        
        if (!$product_id || !is_numeric($product_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid product ID is required'
            ];
        }
        
        $product = $this->product->view_single_product($product_id);
        
        if ($product) {
            return [
                'status' => 'success',
                'data' => $product
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Product not found'
            ];
        }
    }

    /**
     * Filter products by multiple criteria controller (customer-facing)
     * @param array $kwargs - Array containing filters and pagination params
     * @return array - Response array with status and data
     */
    public function filter_composite_ctr($kwargs)
    {
        $filters = $kwargs['filters'] ?? [];
        $limit = $kwargs['limit'] ?? null;
        $offset = $kwargs['offset'] ?? null;
        
        $products = $this->product->filter_products_composite($filters, $limit, $offset);
        $total_count = $this->product->get_filtered_product_count($filters);
        
        if ($products !== false) {
            return [
                'status' => 'success',
                'data' => $products,
                'count' => count($products),
                'total' => $total_count
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to filter products'
            ];
        }
    }

    /**
     * Get filter options controller (categories and brands)
     * @return array - Response with categories and brands
     */
    public function get_filter_options_ctr()
    {
        $categories = $this->product->get_all_categories();
        $brands = $this->product->get_all_brands();
        
        return [
            'status' => 'success',
            'categories' => $categories ? $categories : [],
            'brands' => $brands ? $brands : []
        ];
    }
}
?>
