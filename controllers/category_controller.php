<?php

require_once '../classes/category_class.php';

/**
 * Category Controller - Handles category business logic
 * Creates instances of category class and runs methods
 */
class CategoryController
{
    private $category;

    public function __construct()
    {
        $this->category = new Category();
    }

    /**
     * Add category controller method
     * @param array $kwargs - Array containing category data
     * @return array - Response array with status and message
     */
    public function add_category_ctr($kwargs)
    {
        $cat_name = trim($kwargs['cat_name'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate input
        if (empty($cat_name)) {
            return [
                'status' => 'error',
                'message' => 'Category name is required'
            ];
        }

        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        if (strlen($cat_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Category name must be less than 100 characters'
            ];
        }

        // Call category class method
        $result = $this->category->add_category($cat_name, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Category added successfully',
                'category_id' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Category name already exists or failed to add'
            ];
        }
    }

    /**
     * Get all categories controller method
     * @param array $kwargs - Array containing user ID
     * @return array - Response array with status and data
     */
    public function get_all_categories_ctr($kwargs)
    {
        $user_id = $kwargs['user_id'] ?? null;
        
        if (!$user_id || !is_numeric($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Valid user ID is required'
            ];
        }

        $categories = $this->category->get_all_categories($user_id);
        
        if ($categories !== false) {
            return [
                'status' => 'success',
                'data' => $categories,
                'count' => count($categories)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch categories'
            ];
        }
    }

    /**
     * Get single category controller method
     * @param array $kwargs - Array containing category ID and user ID
     * @return array - Response array with status and data
     */
    public function get_category_ctr($kwargs)
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

        $category = $this->category->get_category($cat_id, $user_id);
        
        if ($category) {
            return [
                'status' => 'success',
                'data' => $category
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Category not found'
            ];
        }
    }

    /**
     * Update category controller method
     * @param array $kwargs - Array containing category data
     * @return array - Response array with status and message
     */
    public function update_category_ctr($kwargs)
    {
        $cat_id = $kwargs['cat_id'] ?? null;
        $cat_name = trim($kwargs['cat_name'] ?? '');
        $user_id = $kwargs['user_id'] ?? null;
        
        // Validate input
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

        if (empty($cat_name)) {
            return [
                'status' => 'error',
                'message' => 'Category name is required'
            ];
        }

        if (strlen($cat_name) > 100) {
            return [
                'status' => 'error',
                'message' => 'Category name must be less than 100 characters'
            ];
        }

        // Call category class method
        $result = $this->category->update_category($cat_id, $cat_name, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Category updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Category name already exists or failed to update'
            ];
        }
    }

    /**
     * Delete category controller method
     * @param array $kwargs - Array containing category ID and user ID
     * @return array - Response array with status and message
     */
    public function delete_category_ctr($kwargs)
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

        // Call category class method
        $result = $this->category->delete_category($cat_id, $user_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Category deleted successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Cannot delete category (may be in use by products or not found)'
            ];
        }
    }

    /**
     * Search categories controller method
     * @param array $kwargs - Array containing search term and user ID
     * @return array - Response array with status and data
     */
    public function search_categories_ctr($kwargs)
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

        $categories = $this->category->search_categories($search_term, $user_id);
        
        if ($categories !== false) {
            return [
                'status' => 'success',
                'data' => $categories,
                'count' => count($categories)
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to search categories'
            ];
        }
    }
}
?>
