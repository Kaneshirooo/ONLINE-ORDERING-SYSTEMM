<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Add inventory table if it doesn't exist
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
$conn->query($inventory_table);

// Handle inventory update
if(isset($_POST['update_inventory'])) {
    foreach($_POST['stock_quantity'] as $product_id => $quantity) {
        $product_id = intval($product_id);
        $quantity = intval($quantity);
        $threshold = intval($_POST['low_stock_threshold'][$product_id]);
        
        // Check if inventory record exists
        $check_sql = "SELECT * FROM inventory WHERE product_id = $product_id";
        $check_result = $conn->query($check_sql);
        
        if($check_result && $check_result->num_rows > 0) {
            // Update existing record
            $update_sql = "UPDATE inventory SET stock_quantity = $quantity, low_stock_threshold = $threshold, 
                          last_restock_date = CURRENT_TIMESTAMP WHERE product_id = $product_id";
            $conn->query($update_sql);
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO inventory (product_id, stock_quantity, low_stock_threshold, last_restock_date) 
                          VALUES ($product_id, $quantity, $threshold, CURRENT_TIMESTAMP)";
            $conn->query($insert_sql);
        }
    }
    
    $success_message = "Inventory updated successfully";
}

// Get products with inventory data
$sql = "SELECT p.*, IFNULL(i.stock_quantity, 0) as stock_quantity, IFNULL(i.low_stock_threshold, 10) as low_stock_threshold, 
        i.last_restock_date
        FROM products p
        LEFT JOIN inventory i ON p.id = i.product_id
        ORDER BY p.category, p.name";
$result = $conn->query($sql);

// Get low stock products
$low_stock_sql = "SELECT p.*, i.stock_quantity, i.low_stock_threshold
                 FROM products p
                 JOIN inventory i ON p.id = i.product_id
                 WHERE i.stock_quantity <= i.low_stock_threshold
                 ORDER BY i.stock_quantity ASC";
$low_stock_result = $conn->query($low_stock_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Café Online</title>
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
                    <a href="manage_products.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-cup-hot me-2"></i> Manage Products
                    </a>
                    <a href="manage_orders.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-bag me-2"></i> Manage Orders
                    </a>
                    <a href="sales_analytics.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-graph-up me-2"></i> Sales Analytics
                    </a>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <a href="inventory_management.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-box-seam me-2"></i> Inventory Management
                    </a>
                    <a href="customer_loyalty.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-award me-2"></i> Customer Loyalty
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <h1>Inventory Management</h1>
                
                <?php if(isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if($low_stock_result && $low_stock_result->num_rows > 0): ?>
                <div class="alert alert-warning">
                    <h5><i class="bi bi-exclamation-triangle-fill"></i> Low Stock Alert</h5>
                    <p>The following products are running low on stock:</p>
                    <ul>
                        <?php while($product = $low_stock_result->fetch_assoc()): ?>
                            <li>
                                <strong><?php echo $product['name']; ?></strong> - 
                                <?php echo $product['stock_quantity']; ?> left 
                                (Threshold: <?php echo $product['low_stock_threshold']; ?>)
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Manage Inventory</h5>
                    </div>
                    <div class="card-body">
                        <form action="inventory_management.php" method="post">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Current Stock</th>
                                            <th>Low Stock Threshold</th>
                                            <th>Last Restock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($result->num_rows > 0): ?>
                                            <?php while($product = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-thumbnail me-2">
                                                            <?php echo $product['name']; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if($product['category'] == 'coffee'): ?>
                                                            <span class="badge bg-secondary"><i class="bi bi-cup-hot"></i> Coffee</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark"><i class="bi bi-pie-chart"></i> Pastry</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="stock_quantity[<?php echo $product['id']; ?>]" value="<?php echo $product['stock_quantity']; ?>" min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="low_stock_threshold[<?php echo $product['id']; ?>]" value="<?php echo $product['low_stock_threshold']; ?>" min="1">
                                                    </td>
                                                    <td>
                                                        <?php echo $product['last_restock_date'] ? date('M d, Y', strtotime($product['last_restock_date'])) : 'Never'; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($product['stock_quantity'] <= 0): ?>
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        <?php elseif($product['stock_quantity'] <= $product['low_stock_threshold']): ?>
                                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">In Stock</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No products found</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" name="update_inventory" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Inventory
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Inventory Tips</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Stock Levels</h5>
                                        <p class="card-text">Set appropriate stock levels based on product popularity and shelf life.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Regular Updates</h5>
                                        <p class="card-text">Update inventory regularly to ensure accurate stock information.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Low Stock Alerts</h5>
                                        <p class="card-text">Set appropriate thresholds to receive alerts before running out of stock.</p>
                                    </div>
                                </div>
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
