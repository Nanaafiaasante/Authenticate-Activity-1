<?php
session_start();
require_once '../controllers/order_controller.php';
require_once '../controllers/product_controller.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$planner_id = $_SESSION['customer_id'];

// Get planner's products
$product_controller = new ProductController();
$result = $product_controller->view_user_products_ctr(['user_id' => $planner_id]);

if ($result['status'] !== 'success' || !$result['data'] || count($result['data']) == 0) {
    echo json_encode([
        'status' => 'success',
        'analytics' => [
            'total_revenue' => 0,
            'total_orders' => 0,
            'total_items_sold' => 0,
            'top_product' => 'N/A',
            'recent_orders' => []
        ]
    ]);
    exit;
}

$products = $result['data'];

// Get product IDs
$product_ids = array_column($products, 'product_id');

// Calculate sales analytics
$total_revenue = 0;
$total_items_sold = 0;
$product_sales = [];
$recent_orders_data = [];

// Get all orders with planner's products
$all_orders = get_all_orders_ctr();

if ($all_orders) {
    foreach ($all_orders as $order) {
        $order_details = get_order_details_ctr($order['order_id']);
        
        if ($order_details) {
            $order_has_planner_product = false;
            $order_total = 0;
            $order_items = 0;
            
            foreach ($order_details as $detail) {
                if (in_array($detail['product_id'], $product_ids)) {
                    $order_has_planner_product = true;
                    $item_total = $detail['product_price'] * $detail['qty'];
                    $total_revenue += $item_total;
                    $total_items_sold += $detail['qty'];
                    $order_total += $item_total;
                    $order_items += $detail['qty'];
                    
                    // Track product sales
                    if (!isset($product_sales[$detail['product_id']])) {
                        $product_sales[$detail['product_id']] = [
                            'name' => $detail['product_title'],
                            'count' => 0,
                            'revenue' => 0
                        ];
                    }
                    $product_sales[$detail['product_id']]['count'] += $detail['qty'];
                    $product_sales[$detail['product_id']]['revenue'] += $item_total;
                }
            }
            
            if ($order_has_planner_product) {
                $recent_orders_data[] = [
                    'order_id' => $order['order_id'],
                    'invoice_no' => $order['invoice_no'],
                    'order_date' => $order['order_date'],
                    'total' => $order_total,
                    'items' => $order_items
                ];
            }
        }
    }
}

// Sort recent orders by date
usort($recent_orders_data, function($a, $b) {
    return strtotime($b['order_date']) - strtotime($a['order_date']);
});

// Get top 5 recent orders
$recent_orders = array_slice($recent_orders_data, 0, 5);

// Get top selling product
$top_product = 'N/A';
if (!empty($product_sales)) {
    uasort($product_sales, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    $top_product_data = reset($product_sales);
    $top_product = $top_product_data['name'];
}

echo json_encode([
    'status' => 'success',
    'analytics' => [
        'total_revenue' => $total_revenue,
        'total_orders' => count($recent_orders_data),
        'total_items_sold' => $total_items_sold,
        'top_product' => $top_product,
        'recent_orders' => $recent_orders,
        'product_sales' => array_values($product_sales)
    ]
]);
?>
