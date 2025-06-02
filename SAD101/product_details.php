<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

// Get product ID from URL parameter
if(!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: products.php");
    exit();
}

$product = $result->fetch_assoc();
$card_class = $product["category"] == "coffee" ? "coffee-card" : "pastry-card";
$badge_class = $product["category"] == "coffee" ? "coffee-badge" : "pastry-badge";
$badge_text = $product["category"] == "coffee" ? "Coffee" : "Pastry";
$badge_icon = $product["category"] == "coffee" ? "bi-cup-hot" : "bi-pie-chart";
$title_class = $product["category"] == "coffee" ? "text-coffee" : "text-pastry";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .text-coffee { color: #6f4e37; }
        .text-pastry { color: #d35400; }
        
        .product-details-card {
            border-radius: 12px;
            overflow: hidden;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .product-details-coffee {
            background-color: #f5ebe0;
        }
        
        .product-details-pastry {
            background-color: #fef6e4;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <div class="card product-details-card <?php echo $product["category"] == "coffee" ? "product-details-coffee" : "product-details-pastry"; ?>">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-5">
                        <div class="position-relative">
                            <span class="product-badge <?php echo $badge_class; ?>">
                                <i class="bi <?php echo $badge_icon; ?>"></i> <?php echo $badge_text; ?>
                            </span>
                            <img src="<?php echo $product['image']; ?>" class="img-fluid rounded product-detail-image" alt="<?php echo $product['name']; ?>">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h1 class="<?php echo $title_class; ?>"><?php echo $product['name']; ?></h1>
                        <p class="lead"><?php echo $product['description']; ?></p>
                        <p class="price-large">₱<?php echo $product['price']; ?></p>
                        
                        <div class="card mb-4">
                            <div class="card-header <?php echo $product["category"] == "coffee" ? "bg-coffee text-white" : "bg-pastry text-white"; ?>" style="background-color: <?php echo $product["category"] == "coffee" ? "#6f4e37" : "#d35400"; ?>">
                                <h5 class="mb-0">Ingredients</h5>
                            </div>
                            <div class="card-body">
                                <p><?php echo $product['ingredients']; ?></p>
                            </div>
                        </div>
                        
                        <form action="add_to_cart.php" method="post" class="d-flex align-items-center">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="input-group me-3" style="width: 130px;">
                                <button type="button" class="btn btn-outline-secondary" onclick="decrementQuantity()">-</button>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-secondary" onclick="incrementQuantity()">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        
                        <div class="mt-4">
                            <a href="products.php?category=<?php echo $product['category']; ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to <?php echo ucfirst($product['category']); ?> Menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function incrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue < 10) {
                quantityInput.value = currentValue + 1;
            }
        }
        
        function decrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }
    </script>
</body>
</html>
