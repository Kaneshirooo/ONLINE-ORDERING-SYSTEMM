<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin only (staff cannot access)
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    if(isset($_SESSION['role']) && $_SESSION['role'] == 'staff') {
        header("Location: manage_orders.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }
}

$role = $_SESSION['role'];

// Enhanced function to get highly specific product images with 100% coverage
function getProductImage($name, $category) {
    $name = strtolower(trim($name));
    $category = strtolower(trim($category));
    
    // COFFEE CATEGORY - Every coffee type gets a specific image
    if($category == 'coffee' || strpos($name, 'coffee') !== false) {
        
        // ESPRESSO FAMILY
        if(strpos($name, 'espresso') !== false) {
            if(strpos($name, 'double') !== false || strpos($name, 'doppio') !== false) 
                return 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'lungo') !== false) 
                return 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'ristretto') !== false) 
                return 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&h=400&fit=crop&auto=format';
        }
        
        // LATTE FAMILY
        if(strpos($name, 'latte') !== false) {
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'caramel') !== false) 
                return 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'hazelnut') !== false) 
                return 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'coconut') !== false) 
                return 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'matcha') !== false) 
                return 'https://images.unsplash.com/photo-1515823064-d6e0c04681a7?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400&h=400&fit=crop&auto=format';
        }
        
        // CAPPUCCINO FAMILY
        if(strpos($name, 'cappuccino') !== false) {
            if(strpos($name, 'dry') !== false) 
                return 'https://images.unsplash.com/photo-1545665225-b23b99e4d45e?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'wet') !== false) 
                return 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'iced') !== false) 
                return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=400&fit=crop&auto=format';
        }
        
        // AMERICANO FAMILY
        if(strpos($name, 'americano') !== false) {
            if(strpos($name, 'iced') !== false) 
                return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'long') !== false) 
                return 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=400&fit=crop&auto=format';
        }
        
        // MOCHA FAMILY
        if(strpos($name, 'mocha') !== false) {
            if(strpos($name, 'white') !== false) 
                return 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'dark') !== false) 
                return 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'iced') !== false) 
                return 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop&auto=format';
        }
        
        // MACCHIATO FAMILY
        if(strpos($name, 'macchiato') !== false) {
            if(strpos($name, 'caramel') !== false) 
                return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1546173159-315724a31696?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&h=400&fit=crop&auto=format';
        }
        
        // COLD COFFEE DRINKS
        if(strpos($name, 'frappuccino') !== false || strpos($name, 'frappe') !== false) {
            if(strpos($name, 'chocolate') !== false || strpos($name, 'mocha') !== false) 
                return 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1546173159-315724a31696?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'caramel') !== false) 
                return 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop&auto=format';
        }
        
        if(strpos($name, 'cold brew') !== false) {
            if(strpos($name, 'nitro') !== false) 
                return 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop&auto=format';
        }
        
        if(strpos($name, 'iced coffee') !== false || (strpos($name, 'iced') !== false && strpos($name, 'coffee') !== false)) {
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'caramel') !== false) 
                return 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop&auto=format';
        }
        
        // SPECIALTY COFFEE
        if(strpos($name, 'turkish') !== false) 
            return 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400&h=400&fit=crop&auto=format';
        if(strpos($name, 'french press') !== false) 
            return 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400&h=400&fit=crop&auto=format';
        if(strpos($name, 'pour over') !== false) 
            return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
        if(strpos($name, 'drip') !== false) 
            return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
        
        // FLAVORED COFFEE
        if(strpos($name, 'vanilla') !== false) 
            return 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=400&h=400&fit=crop&auto=format';
        if(strpos($name, 'caramel') !== false) 
            return 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=400&h=400&fit=crop&auto=format';
        if(strpos($name, 'hazelnut') !== false) 
            return 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=400&h=400&fit=crop&auto=format';
        
        // DEFAULT COFFEE - Any coffee not matched above
        return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
    }
    
    // PASTRY CATEGORY - Every pastry type gets a specific image
    if($category == 'pastry' || strpos($name, 'pastry') !== false) {
        
        // CROISSANT FAMILY
        if(strpos($name, 'croissant') !== false) {
            if(strpos($name, 'chocolate') !== false || strpos($name, 'pain au chocolat') !== false) 
                return 'https://images.unsplash.com/photo-1549903072-7e6e0bedb7fb?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'almond') !== false) 
                return 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'ham') !== false || strpos($name, 'cheese') !== false) 
                return 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'butter') !== false || strpos($name, 'plain') !== false) 
                return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
        }
        
        // MUFFIN FAMILY
        if(strpos($name, 'muffin') !== false) {
            if(strpos($name, 'blueberry') !== false) 
                return 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'chocolate') !== false) 
                return 'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'banana') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'bran') !== false) 
                return 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'lemon') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400&h=400&fit=crop&auto=format';
        }
        
        // DONUT FAMILY
        if(strpos($name, 'donut') !== false || strpos($name, 'doughnut') !== false) {
            if(strpos($name, 'glazed') !== false) 
                return 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'chocolate') !== false) 
                return 'https://images.unsplash.com/photo-1576618148400-f54bed99fcfd?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'sprinkles') !== false || strpos($name, 'rainbow') !== false) 
                return 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'jelly') !== false || strpos($name, 'filled') !== false) 
                return 'https://images.unsplash.com/photo-1576618148400-f54bed99fcfd?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=400&fit=crop&auto=format';
        }
        
        // CAKE FAMILY
        if(strpos($name, 'cake') !== false) {
            if(strpos($name, 'chocolate') !== false) 
                return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'vanilla') !== false) 
                return 'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'red velvet') !== false) 
                return 'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'cheesecake') !== false) 
                return 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'carrot') !== false) 
                return 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'lemon') !== false) 
                return 'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=400&fit=crop&auto=format';
        }
        
        // COOKIE FAMILY
        if(strpos($name, 'cookie') !== false) {
            if(strpos($name, 'chocolate chip') !== false) 
                return 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'oatmeal') !== false) 
                return 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'sugar') !== false) 
                return 'https://images.unsplash.com/photo-1548365328-8c6db3220e4c?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'snickerdoodle') !== false) 
                return 'https://images.unsplash.com/photo-1548365328-8c6db3220e4c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=400&h=400&fit=crop&auto=format';
        }
        
        // BAGEL FAMILY
        if(strpos($name, 'bagel') !== false) {
            if(strpos($name, 'everything') !== false) 
                return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'sesame') !== false) 
                return 'https://images.unsplash.com/photo-1571167530149-c72f2b3d711f?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'plain') !== false) 
                return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=400&fit=crop&auto=format';
        }
        
        // SANDWICH FAMILY
        if(strpos($name, 'sandwich') !== false) {
            if(strpos($name, 'club') !== false) 
                return 'https://images.unsplash.com/photo-1553909489-cd47e0ef937f?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'grilled') !== false) 
                return 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'panini') !== false) 
                return 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1553909489-cd47e0ef937f?w=400&h=400&fit=crop&auto=format';
        }
        
        // BREAD FAMILY
        if(strpos($name, 'bread') !== false) {
            if(strpos($name, 'sourdough') !== false) 
                return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'baguette') !== false) 
                return 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'focaccia') !== false) 
                return 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&h=400&fit=crop&auto=format';
        }
        
        // DANISH FAMILY
        if(strpos($name, 'danish') !== false) {
            if(strpos($name, 'cheese') !== false) 
                return 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'fruit') !== false || strpos($name, 'berry') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
        }
        
        // SCONE FAMILY
        if(strpos($name, 'scone') !== false) {
            if(strpos($name, 'blueberry') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'cranberry') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
        }
        
        // PIE FAMILY
        if(strpos($name, 'pie') !== false) {
            if(strpos($name, 'apple') !== false) 
                return 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=400&h=400&fit=crop&auto=format';
            if(strpos($name, 'cherry') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=400&h=400&fit=crop&auto=format';
        }
        
        // TART FAMILY
        if(strpos($name, 'tart') !== false) {
            if(strpos($name, 'fruit') !== false) 
                return 'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=400&h=400&fit=crop&auto=format';
            return 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400&h=400&fit=crop&auto=format';
        }
        
        // DEFAULT PASTRY - Any pastry not matched above
        return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
    }
    
    // FALLBACK BASED ON KEYWORDS (if category is not set properly)
    if(strpos($name, 'coffee') !== false || strpos($name, 'espresso') !== false || 
       strpos($name, 'latte') !== false || strpos($name, 'cappuccino') !== false) {
        return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
    }
    
    if(strpos($name, 'cake') !== false || strpos($name, 'muffin') !== false || 
       strpos($name, 'cookie') !== false || strpos($name, 'bread') !== false) {
        return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
    }
    
    // ABSOLUTE FALLBACK - Default food image
    return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
}

// Function to get product icon based on name and category
function getProductIcon($name, $category) {
    $name = strtolower($name);
    $category = strtolower($category);
    
    // Coffee icons
    if($category == 'coffee') {
        if(strpos($name, 'espresso') !== false) return '☕';
        if(strpos($name, 'latte') !== false) return '🥛';
        if(strpos($name, 'cappuccino') !== false) return '☕';
        if(strpos($name, 'americano') !== false) return '☕';
        if(strpos($name, 'mocha') !== false) return '🍫';
        if(strpos($name, 'macchiato') !== false) return '☕';
        if(strpos($name, 'frappuccino') !== false || strpos($name, 'frappe') !== false) return '🥤';
        if(strpos($name, 'cold brew') !== false) return '🧊';
        if(strpos($name, 'iced') !== false) return '🧊';
        return '☕'; // Default coffee
    }
    
    // Pastry icons
    if($category == 'pastry') {
        if(strpos($name, 'croissant') !== false) return '🥐';
        if(strpos($name, 'muffin') !== false) return '🧁';
        if(strpos($name, 'donut') !== false || strpos($name, 'doughnut') !== false) return '🍩';
        if(strpos($name, 'cake') !== false) return '🍰';
        if(strpos($name, 'cookie') !== false) return '🍪';
        if(strpos($name, 'bagel') !== false) return '🥯';
        if(strpos($name, 'sandwich') !== false) return '🥪';
        if(strpos($name, 'bread') !== false) return '🍞';
        if(strpos($name, 'danish') !== false) return '🥐';
        if(strpos($name, 'scone') !== false) return '🥐';
        return '🥐'; // Default pastry
    }
    
    return '🍽️'; // Default food icon
}

// Handle product operations
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['add_product'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $category = $conn->real_escape_string($_POST['category']);
        
        $sql = "INSERT INTO products (name, description, price, category, image) VALUES ('$name', '$description', $price, '$category', 'auto-generated')";
        if($conn->query($sql) === TRUE) {
            $success = "Product added successfully!";
        } else {
            $error = "Error adding product: " . $conn->error;
        }
    }
    
    if(isset($_POST['update_product'])) {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $category = $conn->real_escape_string($_POST['category']);
        
        $sql = "UPDATE products SET name='$name', description='$description', price=$price, category='$category' WHERE id=$id";
        if($conn->query($sql) === TRUE) {
            $success = "Product updated successfully!";
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
    
    if(isset($_POST['delete_product'])) {
        $id = intval($_POST['id']);
        
        // First check if product exists
        $check_sql = "SELECT name FROM products WHERE id = $id";
        $check_result = $conn->query($check_sql);
        
        if($check_result && $check_result->num_rows > 0) {
            // Check if product is in any orders (optional - you might want to prevent deletion)
            $order_check_sql = "SELECT COUNT(*) as order_count FROM order_items WHERE product_id = $id";
            $order_check_result = $conn->query($order_check_sql);
            $order_count = $order_check_result->fetch_assoc()['order_count'];
        
            if($order_count > 0) {
                $error = "Cannot delete product: It has been ordered " . $order_count . " times. Consider marking it as unavailable instead.";
            } else {
                // Delete from cart first (foreign key constraint)
                $delete_cart_sql = "DELETE FROM cart WHERE product_id = $id";
                $conn->query($delete_cart_sql);
            
                // Delete from inventory
                $delete_inventory_sql = "DELETE FROM inventory WHERE product_id = $id";
                $conn->query($delete_inventory_sql);
            
                // Finally delete the product
                $delete_sql = "DELETE FROM products WHERE id = $id";
                if($conn->query($delete_sql) === TRUE) {
                    if($conn->affected_rows > 0) {
                        $success = "Product deleted successfully!";
                    } else {
                        $error = "Product not found or already deleted.";
                    }
                } else {
                    $error = "Error deleting product: " . $conn->error;
                }
            }
        } else {
            $error = "Product not found.";
        }
    }
}

// Get all products
$products_sql = "SELECT * FROM products ORDER BY category, name";
$products_result = $conn->query($products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?php if(file_exists('admin-style.css')): ?>
    <link rel="stylesheet" href="admin-style.css">
    <?php endif; ?>
    <link rel="stylesheet" href="style.css">
    <style>
    /* Fallback styles if admin-style.css doesn't exist */
    .admin-page { background-color: #f8f9fa; }
    .admin-sidebar { 
        width: 250px; 
        background: #343a40; 
        height: 100vh; 
        position: fixed; 
        left: 0; 
        top: 0; 
        z-index: 1000;
    }
    .admin-content { margin-left: 250px; padding: 20px; }
    .admin-product-card { 
        border: 1px solid #dee2e6; 
        border-radius: 10px; 
        overflow: hidden; 
        transition: transform 0.2s;
    }
    .admin-product-card:hover { transform: translateY(-5px); }
    .admin-product-image { 
        width: 100%; 
        height: 200px; 
        object-fit: cover; 
    }
    .admin-text-primary { color: #6f4e37; }
    .btn-admin-primary { 
        background: #6f4e37; 
        border-color: #6f4e37; 
        color: white; 
    }
    .btn-admin-secondary { 
        background: #8b5a3c; 
        border-color: #8b5a3c; 
        color: white; 
    }
    .btn-admin-danger { 
        background: #dc3545; 
        border-color: #dc3545; 
        color: white; 
    }
    </style>
</head>
<body class="admin-page">
    <?php include 'header.php'; ?>
    
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <span class="cafe-icon">☕</span>
            <h4>Café Admin</h4>
            <small>Management Portal</small>
        </div>
        
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="manage_products.php" class="list-group-item list-group-item-action active">
                <i class="bi bi-cup-hot"></i> Manage Products
            </a>
            <a href="manage_orders.php" class="list-group-item list-group-item-action">
                <i class="bi bi-bag"></i> Manage Orders
            </a>
            <a href="sales_analytics.php" class="list-group-item list-group-item-action">
                <i class="bi bi-graph-up"></i> Sales Analytics
            </a>
            <a href="daily_reports.php" class="list-group-item list-group-item-action">
                <i class="bi bi-file-text"></i> Daily Reports
            </a>
            <a href="manage_users.php" class="list-group-item list-group-item-action">
                <i class="bi bi-people"></i> Manage Users
            </a>
            <a href="inventory_management.php" class="list-group-item list-group-item-action">
                <i class="bi bi-box-seam"></i> Inventory Management
            </a>
            <a href="customer_loyalty.php" class="list-group-item list-group-item-action">
                <i class="bi bi-award"></i> Customer Loyalty
            </a>
        </div>
    </div>
    
    <div class="admin-content">
        <div class="admin-content-area">
            <div class="admin-page-header">
                <h1 class="admin-page-title">
                    <i class="bi bi-cup-hot me-2"></i>Manage Products
                </h1>
                <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </button>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <?php if($products_result->num_rows > 0): ?>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="admin-product-card">
                                <div class="position-relative overflow-hidden">
                                    <img src="<?php echo getProductImage($product['name'], $product['category']); ?>" 
                                         alt="<?php echo $product['name']; ?>" class="admin-product-image">
                                    <span class="admin-product-category">
                                        <?php echo getProductIcon($product['name'], $product['category']); ?> 
                                        <?php echo ucfirst($product['category']); ?>
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <h5 class="card-title mb-2 admin-text-primary"><?php echo $product['name']; ?></h5>
                                    <p class="card-text text-muted mb-3" style="font-size: 0.9rem; height: 60px; overflow: hidden;">
                                        <?php echo $product['description']; ?>
                                    </p>
                                    <div class="admin-price-tag mb-3">₱<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-admin-secondary btn-sm" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                            <i class="bi bi-pencil me-1"></i> Edit Product
                                        </button>
                                        <button class="btn btn-admin-danger btn-sm" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>')">
                                            <i class="bi bi-trash me-1"></i> Delete Product
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-cup-hot" style="font-size: 4rem; color: #8b5a3c;"></i>
                            <h3 class="admin-text-primary mt-3">No products found</h3>
                            <p class="text-muted">Start by adding your first product!</p>
                            <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="bi bi-plus-circle me-2"></i>Add Your First Product
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div class="modal fade admin-modal" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="admin-form-label">Product Name</label>
                            <input type="text" class="form-control admin-form-control" id="name" name="name" required 
                                   placeholder="e.g., Vanilla Latte, Chocolate Croissant">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="admin-form-label">Description</label>
                            <textarea class="form-control admin-form-control" id="description" name="description" rows="3" required
                                      placeholder="Describe your product in detail..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="admin-form-label">Price (₱)</label>
                            <input type="number" class="form-control admin-form-control" id="price" name="price" step="0.01" required
                                   placeholder="0.00">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="admin-form-label">Category</label>
                            <select class="form-control admin-form-control" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="coffee">☕ Coffee</option>
                                <option value="pastry">🥐 Pastry</option>
                            </select>
                        </div>
                        <div class="admin-alert admin-alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Smart Image System:</strong> Product images are automatically selected based on the product name and category for the best visual match.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-admin-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div class="modal fade admin-modal" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #8b5a3c, #6f4e37);">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" id="editProductForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="admin-form-label">Product Name</label>
                            <input type="text" class="form-control admin-form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="admin-form-label">Description</label>
                            <textarea class="form-control admin-form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="admin-form-label">Price (₱)</label>
                            <input type="number" class="form-control admin-form-control" id="edit_price" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_category" class="admin-form-label">Category</label>
                            <select class="form-control admin-form-control" id="edit_category" name="category" required>
                                <option value="coffee">☕ Coffee</option>
                                <option value="pastry">🥐 Pastry</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_product" class="btn" style="background: #8b5a3c; color: white; border-radius: 10px; padding: 0.75rem 1.5rem;">
                            <i class="bi bi-check-circle me-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade admin-modal" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: #dc3545;">
                    <h5 class="modal-title text-white"><i class="bi bi-trash me-2"></i>Delete Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteProductForm">
                    <input type="hidden" id="delete_id" name="id">
                    <input type="hidden" name="delete_product" value="1">
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            <h4 class="mt-3 admin-text-primary">Are you sure?</h4>
                            <p>You are about to delete <strong id="delete_name"></strong>.</p>
                            <p class="text-muted">This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin-danger">
                            <i class="bi bi-trash me-2"></i>Delete Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(product) {
            document.getElementById('edit_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_category').value = product.category;
            
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }
        
        function deleteProduct(id, name) {
            // Add confirmation dialog
            if(confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('delete_name').textContent = name;
        
                new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
            }
        }
        
        // Add loading animation for images
        document.querySelectorAll('.admin-product-image').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
        });
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('show');
        }
        
        // Add mobile menu button for smaller screens
        if (window.innerWidth <= 768) {
            const header = document.querySelector('header');
            if (header) {
                const menuBtn = document.createElement('button');
                menuBtn.innerHTML = '<i class="bi bi-list"></i>';
                menuBtn.className = 'btn btn-admin-primary d-md-none';
                menuBtn.onclick = toggleSidebar;
                header.appendChild(menuBtn);
            }
        }
    </script>
</body>
</html>
