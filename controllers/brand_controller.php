<?php

require_once '../classes/brand_class.php';

/**
 * Brand Controller - Handles brand business logic
 * Creates instances of brand class and runs methods
 * 
 * For VendorConnect Ghana: Manages brands
 */
class BrandController
{
    private $brand;

    public function __construct()
    {
        $this->brand = new Brand();
    }

    /**
     * Add brand controller method
     * @param array $kwargs - Array containing brand data
     * @return array - Response array with status and message
     */
    public function add_brand_ctr($kwargs)
    {
        $brand_name = trim($kwargs['brand_name'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate input
        if (empty($brand_name)) {
            return [
                'status' => 'error',
                'message' => 'Brand name is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        if (strlen($brand_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Brand name must be less than 100 characters'
            ];
        }

        // Call brand class method
        $result = $this->brand->add_brand($brand_name, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Brand added successfully',
                'brand_id' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Brand name already exists'
            ];
        }
    }

    /**
     * Get all brands controller method
     * @param array $kwargs - Array containing user ID
     * @return array - Response array with status and data
     */
    public function get_all_brands_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $brands = $this->brand->get_all_brands($user_id);
        
        if ($brands !== false) {
            return [
                'status' => 'success',
                'data' => $brands,
                'count' => count($brands)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch brands'
            ];
        }
    }

    /**
     * Get single brand controller method
     * @param array $kwargs - Array containing brand ID and user ID
     * @return array - Response array with status and data
     */
    public function get_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? null;
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$brand_id || !is_numeric($brand_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $brand = $this->brand->get_brand($brand_id, $user_id);
        
        if ($brand) {
            return [
                'status' => 'success',
                'data' => $brand
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Brand not found'
            ];
        }
    }

    /**
     * Update brand controller method
     * @param array $kwargs - Array containing brand data
     * @return array - Response array with status and message
     */
    public function update_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? null;
        $brand_name = trim($kwargs['brand_name'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate input
        if (!$brand_id || !is_numeric($brand_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        if (empty($brand_name)) {
            return [
                'status' => 'error',
                'message' => 'Brand name is required'
            ];
        }

        if (strlen($brand_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Brand name must be less than 100 characters'
            ];
        }

        // Call brand class method
        $result = $this->brand->update_brand($brand_id, $brand_name, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Brand updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Brand name already exists or brand not found'
            ];
        }
    }

    /**
     * Delete brand controller method
     * @param array $kwargs - Array containing brand ID and user ID
     * @return array - Response array with status and message
     */
    public function delete_brand_ctr($kwargs)
    {
        $brand_id = $kwargs['brand_id'] ?? null;
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$brand_id || !is_numeric($brand_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid brand ID is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        // Call brand class method
        $result = $this->brand->delete_brand($brand_id, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Brand deleted successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Cannot delete brand (may be in use by products or not found)'
            ];
        }
    }

    /**
     * Search brands controller method
     * @param array $kwargs - Array containing search term and user ID
     * @return array - Response array with status and data
     */
    public function search_brands_ctr($kwargs)
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

        $brands = $this->brand->search_brands($search_term, $user_id);
        
        if ($brands !== false) {
            return [
                'status' => 'success',
                'data' => $brands,
                'count' => count($brands)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to search brands'
            ];
        }
    }

    /**
     * Get brand count controller method
     * @param array $kwargs - Array containing user ID
     * @return array - Response array with status and count
     */
    public function get_brand_count_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $count = $this->brand->get_brand_count($user_id);
        
        if ($count !== false) {
            return [
                'status' => 'success',
                'count' => $count
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to get brand count'
            ];
        }
    }
}
?>
