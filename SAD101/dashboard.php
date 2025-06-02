<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Redirect staff users to manage orders (their only allowed function)
if($role == 'staff') {
    header("Location: manage_orders.php");
    exit();
}

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Handle the case where the user is not found
    echo "User not found.";
    exit();
}

// Fetch dashboard statistics
$total_products_sql = "SELECT COUNT(*) as count FROM products";
$total_products_result = $conn->query($total_products_sql);
$total_products = $total_products_result ? $total_products_result->fetch_assoc()['count'] : 0;

$total_orders_sql = "SELECT COUNT(*) as count FROM orders";
$total_orders_result = $conn->query($total_orders_sql);
$total_orders = $total_orders_result ? $total_orders_result->fetch_assoc()['count'] : 0;

$total_users_sql = "SELECT COUNT(*) as count FROM users WHERE role = 'customer'";
$total_users_result = $conn->query($total_users_sql);
$total_users = $total_users_result ? $total_users_result->fetch_assoc()['count'] : 0;

$total_sales_sql = "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'";
$total_sales_result = $conn->query($total_sales_sql);
$total_sales = $total_sales_result ? ($total_sales_result->fetch_assoc()['total'] ?? 0) : 0;

$today_orders_sql = "SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()";
$today_orders_result = $conn->query($today_orders_sql);
$today_orders = $today_orders_result ? $today_orders_result->fetch_assoc()['count'] : 0;

$today_sales_sql = "SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE() AND status = 'completed'";
$today_sales_result = $conn->query($today_sales_sql);
$today_sales = $today_sales_result ? ($today_sales_result->fetch_assoc()['total'] ?? 0) : 0;

// Low stock items (assuming you have a stock_quantity column)
$low_stock_sql = "SELECT name, stock_quantity FROM products WHERE stock_quantity < 10 ORDER BY stock_quantity ASC LIMIT 5";
$low_stock_result = $conn->query($low_stock_sql);

// Recent orders
$recent_orders_sql = "SELECT o.id, o.total_amount, o.status, o.created_at, u.username 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      ORDER BY o.created_at DESC LIMIT 5";
$recent_orders_result = $conn->query($recent_orders_sql);

// Top products this month
$top_products_sql = "SELECT p.name, p.category, SUM(oi.quantity) as total_sold 
                     FROM products p 
                     JOIN order_items oi ON p.id = oi.product_id 
                     JOIN orders o ON oi.order_id = o.id 
                     WHERE MONTH(o.created_at) = MONTH(CURDATE()) 
                     AND YEAR(o.created_at) = YEAR(CURDATE())
                     GROUP BY p.id 
                     ORDER BY total_sold DESC LIMIT 5";
$top_products_result = $conn->query($top_products_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .welcome-header {
            background: linear-gradient(135deg, #2c1810 0%, #432818 50%, #6f4e37 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            border: 2px solid #8b5a3c;
        }
        
        .welcome-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(212,165,116,0.2)"/></svg>');
            animation: float 20s infinite linear;
        }
        
        @keyframes float {
            0% { transform: rotate(0deg) translateX(50px) rotate(0deg); }
            100% { transform: rotate(360deg) translateX(50px) rotate(-360deg); }
        }
        
        .quick-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .quick-action-btn {
            background: rgba(212, 165, 116, 0.2);
            border: 2px solid rgba(212, 165, 116, 0.5);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .quick-action-btn:hover {
            background: rgba(212, 165, 116, 0.4);
            color: white;
            transform: translateY(-2px);
            border-color: #d4a574;
        }
        
        .progress-custom {
            height: 8px;
            border-radius: 10px;
            background-color: rgba(67, 40, 24, 0.2);
        }
        
        .progress-custom .progress-bar {
            border-radius: 10px;
            background: linear-gradient(90deg, #8b5a3c, #d4a574);
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
            <a href="dashboard.php" class="list-group-item list-group-item-action active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="manage_products.php" class="list-group-item list-group-item-action">
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
            <!-- Welcome Header -->
            <div class="welcome-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2">Welcome back, <?php echo $_SESSION['username']; ?>! ☕</h1>
                        <p class="mb-3 opacity-75">Here's what's happening at your café today</p>
                        <div class="quick-actions">
                            <a href="manage_products.php" class="quick-action-btn">
                                <i class="bi bi-plus-circle me-2"></i>Add Product
                            </a>
                            <a href="manage_orders.php" class="quick-action-btn">
                                <i class="bi bi-eye me-2"></i>View Orders
                            </a>
                            <a href="daily_reports.php" class="quick-action-btn">
                                <i class="bi bi-printer me-2"></i>Print Report
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="text-white-50">
                            <i class="bi bi-calendar3 me-2"></i><?php echo date('F j, Y'); ?>
                            <br>
                            <i class="bi bi-clock me-2"></i><?php echo date('g:i A'); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="admin-stats-card">
                        <div class="card-body text-center p-4">
                            <div class="admin-stats-icon mx-auto">
                                <i class="bi bi-cup-hot"></i>
                            </div>
                            <div class="admin-stats-number"><?php echo $total_products; ?></div>
                            <h6 class="text-muted mb-0">Total Products</h6>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="admin-stats-card">
                        <div class="card-body text-center p-4">
                            <div class="admin-stats-icon mx-auto">
                                <i class="bi bi-bag-check"></i>
                            </div>
                            <div class="admin-stats-number"><?php echo $total_orders; ?></div>
                            <h6 class="text-muted mb-0">Total Orders</h6>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="admin-stats-card">
                        <div class="card-body text-center p-4">
                            <div class="admin-stats-icon mx-auto">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="admin-stats-number"><?php echo $total_users; ?></div>
                            <h6 class="text-muted mb-0">Customers</h6>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="admin-stats-card">
                        <div class="card-body text-center p-4">
                            <div class="admin-stats-icon mx-auto">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div class="admin-stats-number">₱<?php echo number_format($total_sales, 0); ?></div>
                            <h6 class="text-muted mb-0">Total Sales</h6>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Performance -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5><i class="bi bi-calendar-day me-2"></i>Today's Performance</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="admin-text-secondary"><?php echo $today_orders; ?></h3>
                                        <small class="text-muted">Orders Today</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="admin-text-primary">₱<?php echo number_format($today_sales, 0); ?></h3>
                                        <small class="text-muted">Sales Today</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress progress-custom">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo min(($today_sales / 5000) * 100, 100); ?>%"></div>
                                </div>
                                <small class="text-muted">Daily Goal: ₱5,000</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alert</h5>
                        </div>
                        <div class="card-body">
                            <?php if($low_stock_result && $low_stock_result->num_rows > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while($item = $low_stock_result->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: rgba(139, 90, 60, 0.2);">
                                            <span class="admin-text-primary"><?php echo $item['name']; ?></span>
                                            <span class="admin-badge" style="background: #8b5a3c; color: white;"><?php echo $item['stock_quantity']; ?> left</span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div class="mt-3">
                                    <a href="inventory_management.php" class="btn btn-admin-primary w-100">
                                        <i class="bi bi-box-seam me-2"></i>Manage Inventory
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center admin-text-secondary">
                                    <i class="bi bi-check-circle display-6"></i>
                                    <p class="mt-2 mb-0">All items are well stocked!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders and Top Products -->
            <div class="row">
                <div class="col-md-8">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5><i class="bi bi-clock-history me-2"></i>Recent Orders</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="admin-table table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($recent_orders_result->num_rows > 0): ?>
                                            <?php while($order = $recent_orders_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                                    <td><?php echo $order['username']; ?></td>
                                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="admin-badge bg-<?php 
                                                            switch($order['status']) {
                                                                case 'pending': echo 'warning'; break;
                                                                case 'processing': echo 'info'; break;
                                                                case 'completed': echo 'success'; break;
                                                                case 'cancelled': echo 'danger'; break;
                                                                default: echo 'secondary';
                                                            }
                                                        ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('g:i A', strtotime($order['created_at'])); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No recent orders</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center p-3">
                                <a href="manage_orders.php" class="btn btn-admin-primary">
                                    <i class="bi bi-eye me-2"></i>View All Orders
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5><i class="bi bi-trophy me-2"></i>Top Products</h5>
                        </div>
                        <div class="card-body">
                            <?php if($top_products_result->num_rows > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php $rank = 1; while($product = $top_products_result->fetch_assoc()): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0" style="background: transparent; border-color: rgba(139, 90, 60, 0.2);">
                                            <div>
                                                <span class="admin-badge me-2" style="background: #6f4e37; color: white;"><?php echo $rank++; ?></span>
                                                <span class="admin-text-primary"><?php echo $product['name']; ?></span>
                                                <br><small class="text-muted"><?php echo ucfirst($product['category']); ?></small>
                                            </div>
                                            <span class="admin-badge" style="background: #8b5a3c; color: white;"><?php echo $product['total_sold']; ?> sold</span>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="bi bi-graph-up display-6"></i>
                                    <p class="mt-2 mb-0">No sales data this month</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards on load
            const statsCards = document.querySelectorAll('.admin-stats-card');
            statsCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                }, index * 100);
            });
            
            // Update time every minute
            setInterval(updateTime, 60000);
            
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
                
                const timeElement = document.querySelector('.welcome-header .bi-clock').parentNode;
                if (timeElement) {
                    timeElement.innerHTML = '<i class="bi bi-clock me-2"></i>' + timeString;
                }
            }
        });
    </script>
</body>
</html>
<?php
// Close the database connection
if(isset($conn)) {
    $conn->close();
}
?>
