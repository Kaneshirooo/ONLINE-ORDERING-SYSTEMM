<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Validate quantity
    if($quantity < 1) {
        $quantity = 1;
    } elseif($quantity > 10) {
        $quantity = 10;
    }
    
    // Check if product exists
    $check_product = "SELECT * FROM products WHERE id = $product_id";
    $product_result = $conn->query($check_product);
    
    if($product_result->num_rows == 0) {
        header("Location: products.php");
        exit();
    }
    
    // Check if product is already in cart
    $check_cart = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $cart_result = $conn->query($check_cart);
    
    if($cart_result->num_rows > 0) {
        // Update quantity
        $cart_row = $cart_result->fetch_assoc();
        $new_quantity = $cart_row['quantity'] + $quantity;
        if($new_quantity > 10) {
            $new_quantity = 10;
        }
        
        $update_cart = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $cart_row['id'];
        $conn->query($update_cart);
    } else {
        // Add new item to cart
        $add_to_cart = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $conn->query($add_to_cart);
    }
    
    // Redirect back to previous page or products page
    if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== false) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        header("Location: products.php");
    }
    exit();
} else {
    header("Location: products.php");
    exit();
}
?>
