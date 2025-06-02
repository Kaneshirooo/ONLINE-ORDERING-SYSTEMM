<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_ordering";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If database doesn't exist, create it
if ($conn->connect_errno) {
    $conn = new mysqli($servername, $username, $password);
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS cafe_ordering";
    if ($conn->query($sql) === TRUE) {
        $conn->select_db("cafe_ordering");
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Create tables if they don't exist
$users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$products_table = "CREATE TABLE IF NOT EXISTS products (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('coffee', 'pastries') NOT NULL,
    ingredients TEXT,
    image VARCHAR(255) DEFAULT 'https://placeholder.svg?height=300&width=300',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$orders_table = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

$order_items_table = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

$cart_table = "CREATE TABLE IF NOT EXISTS cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

$inventory_table = "CREATE TABLE IF NOT EXISTS inventory (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    product_id INT(11) NOT NULL,
    stock_quantity INT(11) NOT NULL DEFAULT 0,
    low_stock_threshold INT(11) NOT NULL DEFAULT 10,
    last_restock_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

$loyalty_table = "CREATE TABLE IF NOT EXISTS customer_loyalty (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    points INT(11) NOT NULL DEFAULT 0,
    tier VARCHAR(20) NOT NULL DEFAULT 'bronze',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

$conn->query($users_table);
$conn->query($products_table);
$conn->query($orders_table);
$conn->query($order_items_table);
$conn->query($cart_table);
$conn->query($inventory_table);
$conn->query($loyalty_table);

// Insert default admin user if not exists
$check_admin = "SELECT * FROM users WHERE username = 'admin'";
$result = $conn->query($check_admin);

if ($result->num_rows == 0) {
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (username, password, email, role) 
                    VALUES ('admin', '$admin_password', 'admin@cafe.com', 'admin')";
    $conn->query($insert_admin);
} else {
    // Update existing admin password to ensure it works
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    $update_admin = "UPDATE users SET password = '$admin_password' WHERE username = 'admin'";
    $conn->query($update_admin);
}

// Insert coffee products if not exists
$check_products = "SELECT * FROM products WHERE category = 'coffee'";
$result = $conn->query($check_products);

if ($result->num_rows == 0) {
    $coffee_products = [
        ['Double Espresso', 'Rich and intense double shot of espresso.', 60, 'coffee', 'Freshly ground coffee beans, Water'],
        ['Americano', 'Espresso diluted with hot water.', 80, 'coffee', 'Espresso, Hot water'],
        ['Capuccino', 'Equal parts espresso, steamed milk, and milk foam.', 100, 'coffee', 'Espresso, Steamed milk, Milk foam'],
        ['Latte', 'Espresso with steamed milk and a light layer of foam.', 100, 'coffee', 'Espresso, Steamed milk, Light milk foam'],
        ['Spanish Latte', 'Espresso with condensed milk and steamed milk.', 105, 'coffee', 'Espresso, Condensed milk, Steamed milk'],
        ['Vanilla Latte', 'Latte with vanilla syrup.', 105, 'coffee', 'Espresso, Steamed milk, Vanilla syrup, Light milk foam'],
        ['Hazelnut Latte', 'Latte with hazelnut syrup.', 105, 'coffee', 'Espresso, Steamed milk, Hazelnut syrup, Light milk foam'],
        ['Caramel Latte', 'Latte with caramel syrup.', 105, 'coffee', 'Espresso, Steamed milk, Caramel syrup, Light milk foam'],
        ['Mocha', 'Espresso with chocolate and steamed milk.', 110, 'coffee', 'Espresso, Chocolate syrup, Steamed milk, Whipped cream'],
        ['White Mocha Latte', 'Espresso with white chocolate and steamed milk.', 110, 'coffee', 'Espresso, White chocolate, Steamed milk, Whipped cream'],
        ['Salted Caramel Macchiato', 'Espresso with caramel, salt, and steamed milk.', 115, 'coffee', 'Espresso, Caramel syrup, Sea salt, Steamed milk, Vanilla'],
        ['Matcha Espresso Latte', 'Matcha and espresso with steamed milk.', 115, 'coffee', 'Espresso, Matcha powder, Steamed milk'],
        ['Strawberry Choco', 'Chocolate with strawberry flavor.', 115, 'coffee', 'Chocolate, Strawberry syrup, Steamed milk, Whipped cream'],
        ['Hersheys Choco', 'Hot chocolate made with Hershey\'s chocolate.', 100, 'coffee', 'Hershey\'s chocolate, Steamed milk, Whipped cream'],
        ['London Fog Tea Latte', 'Earl Grey tea with vanilla and steamed milk.', 100, 'coffee', 'Earl Grey tea, Vanilla syrup, Steamed milk'],
        ['Butterfly Pea Tea Latte', 'Butterfly pea flower tea with steamed milk.', 100, 'coffee', 'Butterfly pea flower tea, Steamed milk'],
        ['Matcha Latte', 'Matcha green tea with steamed milk.', 110, 'coffee', 'Matcha powder, Steamed milk'],
        ['Blue Matcha', 'Blue matcha with steamed milk.', 115, 'coffee', 'Blue matcha powder, Steamed milk'],
        ['Strawberry Matcha', 'Matcha with strawberry flavor.', 120, 'coffee', 'Matcha powder, Strawberry syrup, Steamed milk'],
        ['Blueberry Matcha', 'Matcha with blueberry flavor.', 120, 'coffee', 'Matcha powder, Blueberry syrup, Steamed milk'],
        ['Caramel Matcha', 'Matcha with caramel flavor.', 120, 'coffee', 'Matcha powder, Caramel syrup, Steamed milk'],
        ['Strawberry Milk', 'Cold milk with strawberry flavor.', 100, 'coffee', 'Fresh milk, Strawberry syrup'],
        ['Blueberry Milk', 'Cold milk with blueberry flavor.', 100, 'coffee', 'Fresh milk, Blueberry syrup'],
        ['Mango Milk', 'Cold milk with mango flavor.', 100, 'coffee', 'Fresh milk, Mango syrup']
    ];

    foreach ($coffee_products as $product) {
        $name = $conn->real_escape_string($product[0]);
        $description = $conn->real_escape_string($product[1]);
        $price = $product[2];
        $category = $conn->real_escape_string($product[3]);
        $ingredients = $conn->real_escape_string($product[4]);
        
        $insert_product = "INSERT INTO products (name, description, price, category, ingredients) 
                          VALUES ('$name', '$description', $price, '$category', '$ingredients')";
        $conn->query($insert_product);
        
        // Get the inserted product ID
        $product_id = $conn->insert_id;
        
        // Add inventory record with default stock
        $stock = rand(10, 50); // Random stock between 10 and 50
        $insert_inventory = "INSERT INTO inventory (product_id, stock_quantity, low_stock_threshold, last_restock_date) 
                            VALUES ($product_id, $stock, 10, NOW())";
        $conn->query($insert_inventory);
    }
}

// Insert pastry products if not exists
$check_pastries = "SELECT * FROM products WHERE category = 'pastries'";
$result = $conn->query($check_pastries);

if ($result->num_rows == 0) {
    $pastry_products = [
        ['Chocolate Croissant', 'Buttery croissant filled with rich chocolate.', 85, 'pastries', 'Flour, Butter, Chocolate, Sugar, Yeast, Salt'],
        ['Almond Croissant', 'Croissant filled with almond cream and topped with sliced almonds.', 95, 'pastries', 'Flour, Butter, Almond cream, Sliced almonds, Sugar, Yeast, Salt'],
        ['Cinnamon Roll', 'Soft roll with cinnamon sugar filling and cream cheese frosting.', 90, 'pastries', 'Flour, Butter, Cinnamon, Sugar, Cream cheese, Vanilla'],
        ['Blueberry Muffin', 'Moist muffin loaded with fresh blueberries.', 75, 'pastries', 'Flour, Butter, Blueberries, Sugar, Eggs, Milk, Baking powder'],
        ['Chocolate Chip Cookie', 'Classic cookie with chocolate chips.', 60, 'pastries', 'Flour, Butter, Chocolate chips, Brown sugar, White sugar, Eggs, Vanilla'],
        ['Red Velvet Cupcake', 'Moist red velvet cake with cream cheese frosting.', 85, 'pastries', 'Flour, Cocoa powder, Red food coloring, Butter, Sugar, Eggs, Cream cheese'],
        ['Cheese Danish', 'Flaky pastry with sweet cream cheese filling.', 90, 'pastries', 'Flour, Butter, Cream cheese, Sugar, Eggs, Vanilla'],
        ['Apple Turnover', 'Flaky pastry filled with cinnamon apple filling.', 85, 'pastries', 'Flour, Butter, Apples, Cinnamon, Sugar, Lemon juice'],
        ['Banana Bread Slice', 'Moist banana bread with walnuts.', 70, 'pastries', 'Flour, Bananas, Butter, Sugar, Eggs, Walnuts, Baking soda'],
        ['Lemon Tart', 'Buttery crust with tangy lemon filling.', 95, 'pastries', 'Flour, Butter, Eggs, Sugar, Lemons, Cream'],
        ['Chocolate Brownie', 'Rich, fudgy chocolate brownie.', 80, 'pastries', 'Flour, Butter, Chocolate, Sugar, Eggs, Vanilla'],
        ['Strawberry Cheesecake Slice', 'Creamy cheesecake with strawberry topping.', 110, 'pastries', 'Cream cheese, Graham crackers, Butter, Sugar, Eggs, Strawberries']
    ];

    foreach ($pastry_products as $product) {
        $name = $conn->real_escape_string($product[0]);
        $description = $conn->real_escape_string($product[1]);
        $price = $product[2];
        $category = $conn->real_escape_string($product[3]);
        $ingredients = $conn->real_escape_string($product[4]);
        
        $insert_product = "INSERT INTO products (name, description, price, category, ingredients) 
                          VALUES ('$name', '$description', $price, '$category', '$ingredients')";
        $conn->query($insert_product);
        
        // Get the inserted product ID
        $product_id = $conn->insert_id;
        
        // Add inventory record with default stock
        $stock = rand(10, 50); // Random stock between 10 and 50
        $insert_inventory = "INSERT INTO inventory (product_id, stock_quantity, low_stock_threshold, last_restock_date) 
                            VALUES ($product_id, $stock, 10, NOW())";
        $conn->query($insert_inventory);
    }
}
?>
