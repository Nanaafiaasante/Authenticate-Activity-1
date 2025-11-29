<?php
/**
 * Fix Product Ownership
 * Assigns user_id to products that don't have one
 * Run this once to fix existing products
 */

require_once '../settings/db_class.php';

// Create database connection
$db = new db_connection();
$conn = $db->db_conn();

echo "<h2>Fixing Product Ownership</h2>";
echo "<pre>";

// Step 1: Check products without user_id
$check_sql = "SELECT product_id, product_title, product_cat FROM products WHERE user_id IS NULL OR user_id = 0";
$result = mysqli_query($conn, $check_sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " products without user_id:\n\n";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- Product ID: {$row['product_id']}, Title: {$row['product_title']}, Category: {$row['product_cat']}\n";
    }
    
    echo "\n--- Attempting to fix ---\n\n";
    
    // Step 2: Update products by matching category owner
    $update_sql = "UPDATE products p 
                   INNER JOIN categories c ON p.product_cat = c.cat_id 
                   SET p.user_id = c.user_id 
                   WHERE (p.user_id IS NULL OR p.user_id = 0) 
                   AND c.user_id IS NOT NULL AND c.user_id > 0";
    
    if (mysqli_query($conn, $update_sql)) {
        $affected = mysqli_affected_rows($conn);
        echo "✓ Successfully updated $affected products using category ownership\n\n";
    } else {
        echo "✗ Error updating by category: " . mysqli_error($conn) . "\n\n";
    }
    
    // Step 3: If still products without user_id, try brand owner
    $update_sql2 = "UPDATE products p 
                    INNER JOIN brands b ON p.product_brand = b.brand_id 
                    SET p.user_id = b.user_id 
                    WHERE (p.user_id IS NULL OR p.user_id = 0) 
                    AND b.user_id IS NOT NULL AND b.user_id > 0";
    
    if (mysqli_query($conn, $update_sql2)) {
        $affected2 = mysqli_affected_rows($conn);
        if ($affected2 > 0) {
            echo "✓ Successfully updated $affected2 more products using brand ownership\n\n";
        }
    } else {
        echo "✗ Error updating by brand: " . mysqli_error($conn) . "\n\n";
    }
    
    // Step 4: Check for any remaining products without user_id
    $check_remaining = "SELECT COUNT(*) as count FROM products WHERE user_id IS NULL OR user_id = 0";
    $remaining = mysqli_query($conn, $check_remaining);
    $remaining_count = mysqli_fetch_assoc($remaining)['count'];
    
    if ($remaining_count > 0) {
        echo "⚠ WARNING: $remaining_count products still don't have user_id assigned\n";
        echo "You may need to manually assign them to a planner.\n\n";
        echo "To manually fix, run this SQL in phpMyAdmin:\n";
        echo "UPDATE products SET user_id = 1 WHERE user_id IS NULL OR user_id = 0;\n";
        echo "(Replace 1 with the actual planner's customer_id)\n\n";
    } else {
        echo "✓ All products now have user_id assigned!\n\n";
    }
    
} else {
    echo "✓ All products already have user_id assigned. No fixes needed!\n\n";
}

// Step 5: Show final summary
echo "--- Final Product Ownership Summary ---\n\n";
$summary_sql = "SELECT 
    c.customer_name as planner_name,
    c.customer_id,
    COUNT(p.product_id) as product_count
FROM customer c
LEFT JOIN products p ON c.customer_id = p.user_id
WHERE c.user_role = 1
GROUP BY c.customer_id, c.customer_name
ORDER BY product_count DESC";

$summary = mysqli_query($conn, $summary_sql);
if ($summary && mysqli_num_rows($summary) > 0) {
    while ($row = mysqli_fetch_assoc($summary)) {
        echo "Planner: {$row['planner_name']} (ID: {$row['customer_id']}) - {$row['product_count']} products\n";
    }
} else {
    echo "No planners found.\n";
}

echo "</pre>";
echo "<p><a href='../admin/dashboard.php'>Back to Dashboard</a></p>";
?>
