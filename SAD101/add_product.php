<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $ingredients = $conn->real_escape_string($_POST['ingredients']);
    
    // Validate input
    if(empty($name) || empty($description) || empty($price) || empty($category) || empty($ingredients)) {
        $error = "Please fill in all fields";
    } elseif($price <= 0) {
        $error = "Price must be greater than 0";
    } else {
        // Set default image based on category
        $image = "https://placeholder.svg?height=300&width=300";
        
        // Insert product
        $sql = "INSERT INTO products (name, description, price, category, ingredients, image) 
                VALUES ('$name', '$description', $price, '$category', '$ingredients', '$image')";
        
        if($conn->query($sql) === TRUE) {
            $success = "Product added successfully";
            // Clear form data
            $name = $description = $ingredients = '';
            $price = '';
            $category = 'coffee';
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Add New Product</h1>
                    <a href="manage_products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Products
                    </a>
                </div>
                
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form action="add_product.php" method="post" id="productForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($description) ? $description : ''; ?></textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price (₱)</label>
                                    <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" value="<?php echo isset($price) ? $price : ''; ?>" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="coffee" <?php echo (isset($category) && $category == 'coffee') ? 'selected' : ''; ?>>Coffee</option>
                                        <option value="pastries" <?php echo (isset($category) && $category == 'pastries') ? 'selected' : ''; ?>>Pastries</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ingredients" class="form-label">Ingredients</label>
                                <textarea class="form-control" id="ingredients" name="ingredients" rows="3" required><?php echo isset($ingredients) ? $ingredients : ''; ?></textarea>
                                <div class="form-text">Enter ingredients separated by commas.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        document.getElementById('productForm').addEventListener('submit', function(event) {
            let valid = true;
            
            // Validate product name
            const name = document.getElementById('name');
            if (name.value.trim() === '') {
                name.classList.add('is-invalid');
                valid = false;
            } else {
                name.classList.remove('is-invalid');
            }
            
            // Validate price
            const price = document.getElementById('price');
            if (price.value <= 0) {
                price.classList.add('is-invalid');
                valid = false;
            } else {
                price.classList.remove('is-invalid');
            }
            
            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
