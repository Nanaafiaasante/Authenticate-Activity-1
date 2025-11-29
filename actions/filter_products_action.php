<?php
/**
 * Filter Products Action
 * Filters products by category, brand, or composite criteria
 */

session_start();
header('Content-Type: application/json');

require_once '../controllers/product_controller.php';
require_once '../classes/location_class.php';

try {
    $controller = new ProductController();
    
    // Get user's location from session
    $userLocation = Location::getUserLocation();
    
    // Get filter type
    $filter_type = isset($_GET['type']) ? $_GET['type'] : '';
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    
    $offset = ($page - 1) * $per_page;
    
    $result = [];
    
    switch ($filter_type) {
        case 'category':
            $cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
            
            $result = $controller->filter_by_category_ctr([
                'cat_id' => $cat_id,
                'limit' => $per_page,
                'offset' => $offset
            ]);
            break;
            
        case 'brand':
            $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
            
            $result = $controller->filter_by_brand_ctr([
                'brand_id' => $brand_id,
                'limit' => $per_page,
                'offset' => $offset
            ]);
            break;
            
        case 'composite':
            // Build filters array
            $filters = [];
            
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $filters['category'] = (int)$_GET['category'];
            }
            
            if (isset($_GET['brand']) && !empty($_GET['brand'])) {
                $filters['brand'] = (int)$_GET['brand'];
            }
            
            if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
                $filters['min_price'] = (float)$_GET['min_price'];
            }
            
            if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
                $filters['max_price'] = (float)$_GET['max_price'];
            }
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = trim($_GET['search']);
            }
            
            if (isset($_GET['sort']) && !empty($_GET['sort'])) {
                $filters['sort'] = trim($_GET['sort']);
            }
            
            if (isset($_GET['distance_radius']) && $_GET['distance_radius'] !== '') {
                $filters['distance_radius'] = (float)$_GET['distance_radius'];
            }
            
            // Add user location for distance calculations
            if ($userLocation) {
                $filters['user_latitude'] = $userLocation['latitude'];
                $filters['user_longitude'] = $userLocation['longitude'];
            }
            
            $result = $controller->filter_composite_ctr([
                'filters' => $filters,
                'limit' => $per_page,
                'offset' => $offset
            ]);
            
            // Add pagination info for composite
            if ($result['status'] === 'success') {
                $result['page'] = $page;
                $result['per_page'] = $per_page;
                $result['total_pages'] = ceil($result['total'] / $per_page);
            }
            break;
            
        default:
            $result = [
                'status' => 'error',
                'message' => 'Invalid filter type. Use: category, brand, or composite'
            ];
            break;
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
?>
