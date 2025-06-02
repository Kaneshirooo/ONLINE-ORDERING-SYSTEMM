<?php
// This script fixes database issues with inventory and loyalty tables
// Run this script once to repair any existing database problems

// Include database connection
include_once 'db_connect.php';

echo "<h1>Database Repair Script</h1>";
echo "<p>Fixing inventory and loyalty tables...</p>";

// Check if tables exist and create them if they don't
$tables_to_check = [
    'inventory' => "CREATE TABLE IF NOT EXISTS inventory (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        product_id INT(11) NOT NULL,
        stock_quantity INT(11) NOT NULL DEFAULT 0,
        low_stock_threshold INT(11) NOT NULL DEFAULT 10,
        last_restock_date TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",
    
    'customer_loyalty' => "CREATE TABLE IF NOT EXISTS customer_loyalty (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        points INT(11) NOT NULL DEFAULT 0,
        tier VARCHAR(20) NOT NULL DEFAULT 'bronze',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    'loyalty_history' => "CREATE TABLE IF NOT EXISTS loyalty_history (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        points INT(11) NOT NULL,
        reason VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables_to_check as $table => $create_sql) {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE '$table'");
    
    if ($table_check->num_rows == 0) {
        // Table doesn't exist, create it
        if ($conn->query($create_sql)) {
            echo "<p>✅ Created missing table: $table</p>";
        } else {
            echo "<p>❌ Error creating table $table: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>✓ Table $table exists</p>";
        
        // Check for missing foreign keys and fix them
        if ($table == 'inventory') {
            $fk_check = $conn->query("SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                     WHERE TABLE_NAME = 'inventory' 
                                     AND REFERENCED_TABLE_NAME = 'products'");
            
            if ($fk_check->num_rows == 0) {
                // Add missing foreign key
                $conn->query("ALTER TABLE inventory ADD CONSTRAINT fk_inventory_product 
                             FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE");
                echo "<p>✅ Added missing foreign key to inventory table</p>";
            }
        } elseif ($table == 'customer_loyalty' || $table == 'loyalty_history') {
            $fk_check = $conn->query("SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                     WHERE TABLE_NAME = '$table' 
                                     AND REFERENCED_TABLE_NAME = 'users'");
            
            if ($fk_check->num_rows == 0) {
                // Add missing foreign key
                $conn->query("ALTER TABLE $table ADD CONSTRAINT fk_{$table}_user 
                             FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
                echo "<p>✅ Added missing foreign key to $table table</p>";
            }
        }
    }
}

// Check for products without inventory records
$missing_inventory = $conn->query("SELECT p.id FROM products p 
                                  LEFT JOIN inventory i ON p.id = i.product_id 
                                  WHERE i.id IS NULL");

if ($missing_inventory->num_rows > 0) {
    echo "<p>Found " . $missing_inventory->num_rows . " products without inventory records. Adding default inventory...</p>";
    
    while ($row = $missing_inventory->fetch_assoc()) {
        $product_id = $row['id'];
        $stock = rand(10, 50); // Random stock between 10 and 50
        
        $insert_inventory = "INSERT INTO inventory (product_id, stock_quantity, low_stock_threshold, last_restock_date) 
                            VALUES ($product_id, $stock, 10, NOW())";
        
        if ($conn->query($insert_inventory)) {
            echo "<p>✅ Added inventory for product ID: $product_id</p>";
        } else {
            echo "<p>❌ Error adding inventory for product ID $product_id: " . $conn->error . "</p>";
        }
    }
}

// Check for customers without loyalty records
$missing_loyalty = $conn->query("SELECT u.id FROM users u 
                               LEFT JOIN customer_loyalty cl ON u.id = cl.user_id 
                               WHERE u.role = 'customer' AND cl.id IS NULL");

if ($missing_loyalty->num_rows > 0) {
    echo "<p>Found " . $missing_loyalty->num_rows . " customers without loyalty records. Adding default loyalty records...</p>";
    
    while ($row = $missing_loyalty->fetch_assoc()) {
        $user_id = $row['id'];
        
        $insert_loyalty = "INSERT INTO customer_loyalty (user_id, points, tier) 
                          VALUES ($user_id, 0, 'bronze')";
        
        if ($conn->query($insert_loyalty)) {
            echo "<p>✅ Added loyalty record for user ID: $user_id</p>";
        } else {
            echo "<p>❌ Error adding loyalty record for user ID $user_id: " . $conn->error . "</p>";
        }
    }
}

echo "<h2>Database repair completed!</h2>";
echo "<p><a href='index.php'>Return to homepage</a></p>";
?>
