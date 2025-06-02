<?php
session_start();
include_once 'db_connect.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Sample products to add
$sample_products = [
    // Coffee Products
    [
        'name' => 'Classic Americano',
        'description' => 'Rich espresso diluted with hot water for a smooth, bold flavor.',
        'price' => 80.00,
        'category' => 'coffee'
    ],
    [
        'name' => 'Vanilla Latte',
        'description' => 'Creamy steamed milk with espresso and vanilla syrup.',
        'price' => 120.00,
        'category' => 'coffee'
    ],
    [
        'name' => 'Caramel Cappuccino',
        'description' => 'Perfect balance of espresso, steamed milk, and caramel sweetness.',
        'price' => 110.00,
        'category' => 'coffee'
    ],
    [
        'name' => 'Iced Coffee',
        'description' => 'Refreshing cold brew coffee served over ice.',
        'price' => 90.00,
        'category' => 'coffee'
    ],
    [
        'name' => 'Mocha Delight',
        'description' => 'Rich chocolate and espresso blend with whipped cream.',
        'price' => 130.00,
        'category' => 'coffee'
    ],
    
    // Pastry Products
    [
        'name' => 'Butter Croissant',
        'description' => 'Flaky, buttery French pastry baked to golden perfection.',
        'price' => 65.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Chocolate Croissant',
        'description' => 'Classic pain au chocolat with rich dark chocolate filling.',
        'price' => 75.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Blueberry Muffin',
        'description' => 'Moist muffin bursting with fresh blueberries.',
        'price' => 55.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Chocolate Chip Cookie',
        'description' => 'Warm, chewy cookie loaded with chocolate chips.',
        'price' => 45.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Glazed Donut',
        'description' => 'Classic glazed donut with sweet, shiny coating.',
        'price' => 50.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Chocolate Cake Slice',
        'description' => 'Rich, moist chocolate cake with chocolate frosting.',
        'price' => 85.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Almond Croissant',
        'description' => 'Buttery croissant filled with sweet almond cream.',
        'price' => 80.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Banana Muffin',
        'description' => 'Soft, fluffy muffin with real banana pieces.',
        'price' => 60.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Cinnamon Roll',
        'description' => 'Warm cinnamon roll with sweet glaze drizzle.',
        'price' => 70.00,
        'category' => 'pastry'
    ],
    [
        'name' => 'Cheesecake Slice',
        'description' => 'Creamy New York style cheesecake with graham crust.',
        'price' => 95.00,
        'category' => 'pastry'
    ]
];

$success_count = 0;
$error_count = 0;

foreach($sample_products as $product) {
    // Check if product already exists
    $check_sql = "SELECT id FROM products WHERE name = '" . $conn->real_escape_string($product['name']) . "'";
    $check_result = $conn->query($check_sql);
    
    if($check_result->num_rows == 0) {
        // Product doesn't exist, add it
        $insert_sql = "INSERT INTO products (name, description, price, category, image) VALUES (
            '" . $conn->real_escape_string($product['name']) . "',
            '" . $conn->real_escape_string($product['description']) . "',
            " . $product['price'] . ",
            '" . $conn->real_escape_string($product['category']) . "',
            'auto-generated'
        )";
        
        if($conn->query($insert_sql)) {
            $success_count++;
        } else {
            $error_count++;
        }
    }
}

$message = "Added $success_count new products successfully!";
if($error_count > 0) {
    $message .= " ($error_count products already existed or had errors)";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sample Products - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin-style.css">
</head>
<body class="admin-page">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="admin-card">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h2 class="admin-text-primary mt-3">Sample Products Added!</h2>
                        <p class="lead"><?php echo $message; ?></p>
                        
                        <div class="mt-4">
                            <a href="manage_products.php" class="btn btn-admin-primary me-3">
                                <i class="bi bi-cup-hot me-2"></i>Manage Products
                            </a>
                            <a href="products.php" class="btn btn-admin-secondary">
                                <i class="bi bi-eye me-2"></i>View Menu
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                You can now view both coffee and pastry products in your menu!
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
