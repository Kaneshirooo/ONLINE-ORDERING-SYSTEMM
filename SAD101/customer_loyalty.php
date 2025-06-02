<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Add loyalty table if it doesn't exist
$loyalty_table = "CREATE TABLE IF NOT EXISTS customer_loyalty (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    points INT(11) NOT NULL DEFAULT 0,
    tier VARCHAR(20) NOT NULL DEFAULT 'bronze',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($loyalty_table);

// Handle points adjustment
if(isset($_POST['adjust_points']) && isset($_POST['user_id']) && isset($_POST['points'])) {
    $user_id = intval($_POST['user_id']);
    $points = intval($_POST['points']);
    $reason = $conn->real_escape_string($_POST['reason']);
    
    // Check if loyalty record exists
    $check_sql = "SELECT * FROM customer_loyalty WHERE user_id = $user_id";
    $check_result = $conn->query($check_sql);
    
    if($check_result && $check_result->num_rows > 0) {
        $loyalty = $check_result->fetch_assoc();
        $new_points = max(0, $loyalty['points'] + $points); // Ensure points don't go below 0
        
        // Determine tier based on points
        $tier = 'bronze';
        if($new_points >= 1000) {
            $tier = 'platinum';
        } elseif($new_points >= 500) {
            $tier = 'gold';
        } elseif($new_points >= 200) {
            $tier = 'silver';
        }
        
        // Update points and tier
        $update_sql = "UPDATE customer_loyalty SET points = $new_points, tier = '$tier' WHERE user_id = $user_id";
        $conn->query($update_sql);
    } else {
        // Determine tier based on points
        $tier = 'bronze';
        if($points >= 1000) {
            $tier = 'platinum';
        } elseif($points >= 500) {
            $tier = 'gold';
        } elseif($points >= 200) {
            $tier = 'silver';
        }
        
        // Insert new record
        $insert_sql = "INSERT INTO customer_loyalty (user_id, points, tier) VALUES ($user_id, $points, '$tier')";
        $conn->query($insert_sql);
    }
    
    // Add to loyalty history
    $history_sql = "INSERT INTO loyalty_history (user_id, points, reason) VALUES ($user_id, $points, '$reason')";
    $conn->query($history_sql);
    
    $success_message = "Loyalty points adjusted successfully";
}

// Get customers with loyalty data
$sql = "SELECT u.id, u.username, u.email, IFNULL(cl.points, 0) as points, IFNULL(cl.tier, 'bronze') as tier
        FROM users u
        LEFT JOIN customer_loyalty cl ON u.id = cl.user_id
        WHERE u.role = 'customer'
        ORDER BY cl.points DESC";
$result = $conn->query($sql);

// Get top customers
$top_customers_sql = "SELECT u.username, cl.points, cl.tier, COUNT(o.id) as order_count, SUM(o.total_amount) as total_spent
                     FROM customer_loyalty cl
                     JOIN users u ON cl.user_id = u.id
                     JOIN orders o ON u.id = o.user_id
                     WHERE u.role = 'customer'
                     GROUP BY cl.user_id
                     ORDER BY cl.points DESC
                     LIMIT 5";
$top_customers_result = $conn->query($top_customers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Loyalty - Café Online</title>
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
                    <?php if($role == 'admin'): ?>
                    <a href="manage_users.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                    <a href="inventory_management.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam me-2"></i> Inventory Management
                    </a>
                    <?php endif; ?>
                    <a href="customer_loyalty.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-award me-2"></i> Customer Loyalty
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <h1>Customer Loyalty Program</h1>
                
                <?php if(isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Loyalty Tiers</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tier</th>
                                                <th>Points Required</th>
                                                <th>Benefits</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="loyalty-badge loyalty-bronze">Bronze</span></td>
                                                <td>0-199</td>
                                                <td>Basic rewards, birthday offer</td>
                                            </tr>
                                            <tr>
                                                <td><span class="loyalty-badge loyalty-silver">Silver</span></td>
                                                <td>200-499</td>
                                                <td>5% discount, free coffee on 5th visit</td>
                                            </tr>
                                            <tr>
                                                <td><span class="loyalty-badge loyalty-gold">Gold</span></td>
                                                <td>500-999</td>
                                                <td>10% discount, free pastry on 5th visit</td>
                                            </tr>
                                            <tr>
                                                <td><span class="loyalty-badge loyalty-platinum">Platinum</span></td>
                                                <td>1000+</td>
                                                <td>15% discount, free coffee & pastry on 5th visit</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Top Loyal Customers</h5>
                            </div>
                            <div class="card-body">
                                <?php if($top_customers_result && $top_customers_result->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Customer</th>
                                                    <th>Points</th>
                                                    <th>Tier</th>
                                                    <th>Orders</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while($customer = $top_customers_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $customer['username']; ?></td>
                                                        <td><?php echo $customer['points']; ?></td>
                                                        <td>
                                                            <span class="loyalty-badge loyalty-<?php echo $customer['tier']; ?>">
                                                                <?php echo ucfirst($customer['tier']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $customer['order_count']; ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-center">No loyalty data available yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Manage Customer Loyalty</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Points</th>
                                        <th>Tier</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result->num_rows > 0): ?>
                                        <?php while($customer = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $customer['username']; ?></td>
                                                <td><?php echo $customer['email']; ?></td>
                                                <td><?php echo $customer['points']; ?></td>
                                                <td>
                                                    <span class="loyalty-badge loyalty-<?php echo $customer['tier']; ?>">
                                                        <?php echo ucfirst($customer['tier']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustPointsModal<?php echo $customer['id']; ?>">
                                                        <i class="bi bi-plus-slash-minus"></i> Adjust Points
                                                    </button>
                                                    
                                                    <!-- Adjust Points Modal -->
                                                    <div class="modal fade" id="adjustPointsModal<?php echo $customer['id']; ?>" tabindex="-1" aria-labelledby="adjustPointsModalLabel<?php echo $customer['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="adjustPointsModalLabel<?php echo $customer['id']; ?>">Adjust Points for <?php echo $customer['username']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="customer_loyalty.php" method="post">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="user_id" value="<?php echo $customer['id']; ?>">
                                                                        <div class="mb-3">
                                                                            <label for="points<?php echo $customer['id']; ?>" class="form-label">Points to Add/Subtract</label>
                                                                            <input type="number" class="form-control" id="points<?php echo $customer['id']; ?>" name="points" required>
                                                                            <div class="form-text">Enter a positive number to add points or a negative number to subtract points.</div>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="reason<?php echo $customer['id']; ?>" class="form-label">Reason</label>
                                                                            <select class="form-select" id="reason<?php echo $customer['id']; ?>" name="reason" required>
                                                                                <option value="Purchase">Purchase</option>
                                                                                <option value="Referral">Referral</option>
                                                                                <option value="Birthday">Birthday</option>
                                                                                <option value="Promotion">Promotion</option>
                                                                                <option value="Adjustment">Manual Adjustment</option>
                                                                                <option value="Refund">Refund</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="adjust_points" class="btn btn-primary">Save changes</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No customers found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Loyalty Program Tips</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Point Allocation</h5>
                                        <p class="card-text">Award 1 point for every ₱10 spent to encourage larger purchases.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Special Promotions</h5>
                                        <p class="card-text">Run double or triple point promotions during slow business periods.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-lightbulb"></i> Tier Benefits</h5>
                                        <p class="card-text">Clearly communicate tier benefits to customers to encourage loyalty.</p>
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
