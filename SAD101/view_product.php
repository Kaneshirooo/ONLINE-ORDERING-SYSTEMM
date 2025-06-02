<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_products.php");
    exit();
}

$product_id = $_GET['id'];
$role = $_SESSION['role'];

// Get product details
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if($result->num_rows == 0) {
    header("Location: manage_products.php");
    exit();
}

$product = $result->fetch_assoc();
$category_symbol = $product["category"] == "coffee" ? "☕" : "🥐";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a href="manage_products.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-cup-hot me-2"></i> Manage Products
                    </a>
                    <a href="manage_orders.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-2"></i> Manage Orders
                    </a>
                    <?php if($role == 'admin'): ?>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>View Product</h1>
                    <a href="manage_products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Products
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid rounded">
                            </div>
                            <div class="col-md-8">
                                <h2><?php echo $product['name']; ?> <?php echo $category_symbol; ?></h2>
                                <p class="lead"><?php echo $product['description']; ?></p>
                                
                                <div class="mb-3">
                                    <strong>Price:</strong> ₱<?php echo $product['price']; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Category:</strong> <?php echo ucfirst($product['category']); ?> <?php echo $category_symbol; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Ingredients:</strong>
                                    <p><?php echo $product['ingredients']; ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Added on:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?>
                                </div>
                                
                                <?php if($role == 'admin'): ?>
                                <div class="mt-4">
                                    <a href="edit_product.php?id=<?php echo $product_id; ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil"></i> Edit Product
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
