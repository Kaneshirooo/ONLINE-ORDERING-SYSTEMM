<?php
session_start();
include_once 'db_connect.php';

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
                return 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?w=400&h=400&fit=crop&auto=format';
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

// Get all products
$products_sql = "SELECT * FROM products ORDER BY category, name";
$products_result = $conn->query($products_sql);

// Handle add to cart
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Check if item already in cart
    $check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = $conn->query($check_sql);
    
    if($check_result->num_rows > 0) {
        // Update quantity
        $update_sql = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id";
        $conn->query($update_sql);
    } else {
        // Add new item
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $conn->query($insert_sql);
    }
    
    $success = "Item added to cart successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etched Cafe☕ - Premium Coffee & Pastries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 50%, #8b5a3c 100%);
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="coffee" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23coffee)"/></svg>');
            opacity: 0.3;
        }
        
        .product-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(67, 40, 24, 0.15);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(67, 40, 24, 0.25);
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-category {
            background: linear-gradient(135deg, #6f4e37, #432818);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 2;
        }
        
        .price-tag {
            font-size: 1.5rem;
            font-weight: 700;
            color: #432818;
        }
        
        .add-to-cart-btn {
            background: linear-gradient(135deg, #6f4e37, #432818);
            border: none;
            border-radius: 15px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .add-to-cart-btn:hover {
            background: linear-gradient(135deg, #8b5a3c, #6f4e37);
            transform: translateY(-2px);
            color: white;
        }
        
        .quantity-selector {
            border: 2px solid #6f4e37;
            border-radius: 10px;
            padding: 0.5rem;
            width: 80px;
            text-align: center;
        }
        
        .section-title {
            color: #432818;
            font-weight: 700;
            margin-bottom: 3rem;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #6f4e37, #8b5a3c);
            border-radius: 2px;
        }
        
        .products-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, #f8f5f2 0%, #e8e0d7 100%);
        }
        
        .image-overlay {
            position: relative;
            overflow: hidden;
        }
        
        .image-overlay::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.1) 100%);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Etched Cafe☕</h1>
                    <p class="lead mb-4">Discover our premium selection of freshly brewed coffee and artisanal pastries, delivered right to your doorstep.</p>
                    <div class="d-flex gap-3">
                        <a href="#products" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-cup-hot me-2"></i>Order Now
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&h=500&fit=crop" 
                         alt="Coffee" class="img-fluid rounded-circle" style="max-width: 400px; border: 5px solid rgba(255,255,255,0.2);">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Products Section -->
    <section id="products" class="products-section">
        <div class="container">
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <h2 class="section-title text-center">Our Premium Menu</h2>
            
            <div class="row">
                <?php if($products_result->num_rows > 0): ?>
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card">
                                <div class="image-overlay">
                                    <img src="<?php echo getProductImage($product['name'], $product['category']); ?>" 
                                         alt="<?php echo $product['name']; ?>" class="product-image">
                                    <span class="product-category"><?php echo ucfirst($product['category']); ?></span>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-2" style="color: #432818;"><?php echo $product['name']; ?></h5>
                                    <p class="card-text text-muted mb-3"><?php echo $product['description']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="price-tag">₱<?php echo number_format($product['price'], 2); ?></div>
                                        <div class="rating">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-half text-warning"></i>
                                            <small class="text-muted ms-1">(4.5)</small>
                                        </div>
                                    </div>
                                    
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <form method="post" class="d-flex gap-2 align-items-center">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="number" name="quantity" value="1" min="1" max="10" class="quantity-selector">
                                            <button type="submit" name="add_to_cart" class="add-to-cart-btn flex-grow-1">
                                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="login.php" class="add-to-cart-btn text-decoration-none d-block text-center">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login to Order
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-cup-hot" style="font-size: 4rem; color: #8b5a3c;"></i>
                            <h3 style="color: #432818;">No products available</h3>
                            <p class="text-muted">Check back soon for our delicious offerings!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Add loading animation for images
        document.querySelectorAll('.product-image').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
        });
    </script>
</body>
</html>
