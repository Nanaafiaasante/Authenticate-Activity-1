<!DOCTYPE html>
<html>
<head>
    <title>Database Column Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #e7f3ff; padding: 10px; border-left: 3px solid #2196F3; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Order Details Database Check</h1>
    
    <?php
    require_once '../settings/db_class.php';
    
    echo "<h2>1. Checking orderdetails table structure:</h2>";
    
    try {
        $db = new db_connection();
        $conn = $db->db_conn();
        
        // Check if selected_items column exists
        $result = mysqli_query($conn, "DESCRIBE orderdetails");
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $has_selected_items = false;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td><strong>" . $row['Field'] . "</strong></td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
            
            if ($row['Field'] === 'selected_items') {
                $has_selected_items = true;
            }
        }
        echo "</table>";
        
        if ($has_selected_items) {
            echo "<p class='success'>✓ selected_items column EXISTS in orderdetails table</p>";
        } else {
            echo "<p class='error'>✗ selected_items column DOES NOT EXIST in orderdetails table</p>";
            echo "<div class='info'>";
            echo "<strong>Action Required:</strong> Run this SQL in phpMyAdmin:<br><br>";
            echo "<pre>USE `shoppn`;
ALTER TABLE `orderdetails` 
ADD COLUMN `selected_items` TEXT NULL DEFAULT NULL 
COMMENT 'JSON array of selected package items with their names' AFTER `qty`;</pre>";
            echo "</div>";
        }
        
        echo "<h2>2. Checking existing order data:</h2>";
        
        // Get a sample order with details
        $result = mysqli_query($conn, "
            SELECT od.order_id, od.product_id, od.qty, od.selected_items, p.product_title
            FROM orderdetails od
            LEFT JOIN products p ON od.product_id = p.product_id
            LIMIT 5
        ");
        
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Order ID</th><th>Product</th><th>Qty</th><th>Selected Items</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['order_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['product_title']) . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                
                if (empty($row['selected_items'])) {
                    echo "<td><em style='color: orange;'>No items saved (order placed before update)</em></td>";
                } else {
                    echo "<td><pre>" . htmlspecialchars($row['selected_items']) . "</pre></td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No orders found in database</p>";
        }
        
        echo "<h2>3. Checking cart data (for future orders):</h2>";
        
        // Check if cart has selected_items
        $result = mysqli_query($conn, "
            SELECT c.c_id, c.p_id, c.qty, c.selected_items, p.product_title
            FROM cart c
            LEFT JOIN products p ON c.p_id = p.product_id
            WHERE c.selected_items IS NOT NULL
            LIMIT 3
        ");
        
        if (mysqli_num_rows($result) > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Customer ID</th><th>Product</th><th>Qty</th><th>Selected Items</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['c_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['product_title']) . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                echo "<td><pre>" . htmlspecialchars($row['selected_items']) . "</pre></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p class='success'>✓ Cart items with package selections found - new orders will include package items!</p>";
        } else {
            echo "<p>No cart items with selected package items found</p>";
        }
        
        mysqli_close($conn);
        
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
    ?>
    
    <hr>
    <h2>Next Steps:</h2>
    <ol>
        <li>If the <code>selected_items</code> column doesn't exist, run the SQL command shown above in phpMyAdmin</li>
        <li>Old orders (placed before the update) won't have package items data</li>
        <li>Place a NEW test order with package items selected</li>
        <li>Check the order history - the new order should show the selected package items</li>
    </ol>
    
    <p><a href="orders.php">← Back to Order History</a></p>
</body>
</html>
