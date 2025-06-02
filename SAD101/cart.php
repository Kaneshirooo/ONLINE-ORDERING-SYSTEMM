<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details for auto-fill
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Get cart items
$cart_sql = "SELECT c.*, p.name, p.price, p.image, p.category 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_sql);

$total = 0;
$cart_items = [];
while($item = $cart_result->fetch_assoc()) {
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

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

// Handle cart updates
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_quantity'])) {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if($quantity > 0) {
            $update_sql = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
            $conn->query($update_sql);
        } else {
            $delete_sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
            $conn->query($delete_sql);
        }
        
        header("Location: cart.php");
        exit();
    }
    
    if(isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $delete_sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        $conn->query($delete_sql);
        
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container {
            background: linear-gradient(135deg, #f8f5f2 0%, #e8e0d7 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .cart-card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background: white;
        }
        
        .cart-header {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
        }
        
        .cart-item {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background-color: rgba(111, 78, 55, 0.05);
        }
        
        .product-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid #6f4e37;
            background: white;
            color: #6f4e37;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .quantity-btn:hover {
            background: #6f4e37;
            color: white;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.5rem;
        }
        
        .checkout-section {
            background: linear-gradient(135deg, #f8f5f2, #f0e8df);
            border-radius: 15px;
            padding: 2rem;
            position: sticky;
            top: 2rem;
        }
        
        .customer-info-card {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .auto-fill-form {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-control:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 0 0.25rem rgba(111, 78, 55, 0.25);
        }

        .summary-items {
            max-height: 200px; /* Adjust the height as needed */
            overflow-y: auto;
            padding-right: 10px; /* Add some padding for the scrollbar */
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="cart-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <div class="cart-header">
                            <h2><i class="bi bi-cart3 me-2"></i>Your Shopping Cart</h2>
                            <p class="mb-0">Review your items before checkout</p>
                        </div>
                        
                        <?php if(count($cart_items) > 0): ?>
                            <?php foreach($cart_items as $item): ?>
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="<?php echo getProductImage($item['name'], $item['category']); ?>" 
                                                 alt="<?php echo $item['name']; ?>" 
                                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; border: 2px solid #e9ecef;">
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="mb-1"><?php echo $item['name']; ?></h5>
                                            <small class="text-muted"><?php echo ucfirst($item['category']); ?></small>
                                            <div class="text-primary fw-bold">₱<?php echo number_format($item['price'], 2); ?></div>
                                        </div>
                                        <div class="col-md-3">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <div class="quantity-controls">
                                                    <button type="button" class="quantity-btn" onclick="decreaseQuantity(<?php echo $item['id']; ?>)">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                           min="1" class="quantity-input" id="qty_<?php echo $item['id']; ?>"
                                                           onchange="updateQuantity(<?php echo $item['id']; ?>)">
                                                    <button type="button" class="quantity-btn" onclick="increaseQuantity(<?php echo $item['id']; ?>)">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="fw-bold text-success">
                                                ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" name="remove_item" class="btn btn-outline-danger btn-sm">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d;"></i>
                                <h3 class="mt-3">Your cart is empty</h3>
                                <p class="text-muted">Add some delicious items to get started!</p>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <?php if(count($cart_items) > 0): ?>
                        <!-- Customer Information (Auto-filled) -->
                        <div class="customer-info-card">
                            <h5><i class="bi bi-person-circle me-2"></i>Customer Information</h5>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <img src="<?php echo $user['profile_picture'] ?? 'uploads/default-avatar.png'; ?>" 
                                         alt="Profile" style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid white;">
                                </div>
                                <div class="col">
                                    <div class="fw-bold"><?php echo $user['username']; ?></div>
                                    <small class="opacity-75"><?php echo $user['email']; ?></small>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Auto-filled Form -->
                        <div class="auto-fill-form">
                            <h6 class="mb-3"><i class="bi bi-magic me-2"></i>Pre-filled Information</h6>
                            <form id="customerForm">
                                <div class="mb-3">
                                    <label for="customer_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="customer_name" 
                                           value="<?php echo $user['username']; ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="customer_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="customer_email" 
                                           value="<?php echo $user['email']; ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="customer_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="customer_phone" 
                                           value="<?php echo $user['phone'] ?? '+63 912 345 6789'; ?>" 
                                           placeholder="Enter your phone number">
                                </div>
                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Delivery Address</label>
                                    <textarea class="form-control" id="customer_address" rows="2" 
                                              placeholder="Enter your delivery address"><?php echo $user['address'] ?? '123 Coffee Street, Manila, Philippines'; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="special_instructions" class="form-label">Special Instructions</label>
                                    <textarea class="form-control" id="special_instructions" rows="2" 
                                              placeholder="Any special requests or notes...">Please ring the doorbell twice. Thank you!</textarea>
                                </div>
                            </form>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                This information will be automatically filled during checkout.
                            </small>
                        </div>
                        
                        <!-- Checkout Summary -->
                        <div class="checkout-section">
                            <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                            
                            <div class="summary-items">
                                <?php foreach($cart_items as $item): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo getProductImage($item['name'], $item['category']); ?>" 
                                                 alt="<?php echo $item['name']; ?>" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;" class="me-2">
                                            <div>
                                                <small class="fw-bold"><?php echo $item['name']; ?></small>
                                                <br><small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                            </div>
                                        </div>
                                        <span class="fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>₱<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Fee:</span>
                                <span>₱50.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Fee:</span>
                                <span>₱15.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong class="text-primary">₱<?php echo number_format($total + 65, 2); ?></strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Secure checkout with SSL encryption
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function increaseQuantity(cartId) {
            const input = document.getElementById('qty_' + cartId);
            input.value = parseInt(input.value) + 1;
            updateQuantity(cartId);
        }
        
        function decreaseQuantity(cartId) {
            const input = document.getElementById('qty_' + cartId);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateQuantity(cartId);
            }
        }
        
        function updateQuantity(cartId) {
            const quantity = document.getElementById('qty_' + cartId).value;
            
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="cart_id" value="${cartId}">
                <input type="hidden" name="quantity" value="${quantity}">
                <input type="hidden" name="update_quantity" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        // Auto-save form data to localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('customerForm');
            const inputs = form.querySelectorAll('input, textarea');
            
            // Load saved data
            inputs.forEach(input => {
                const savedValue = localStorage.getItem('cart_' + input.id);
                if (savedValue && !input.readOnly) {
                    input.value = savedValue;
                }
                
                // Save data on change
                input.addEventListener('input', function() {
                    if (!this.readOnly) {
                        localStorage.setItem('cart_' + this.id, this.value);
                    }
                });
            });
        });
    </script>
</body>
</html>
