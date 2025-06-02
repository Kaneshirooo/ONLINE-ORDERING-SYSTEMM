<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$today = date('Y-m-d');

// Get today's sales summary
$sales_sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as total_sales,
                AVG(total_amount) as avg_order_value
              FROM orders 
              WHERE DATE(created_at) = '$today' AND status != 'cancelled'";
$sales_result = $conn->query($sales_sql);
$sales_summary = $sales_result->fetch_assoc();

// Get today's orders
$orders_sql = "SELECT o.*, u.username, u.email 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               WHERE DATE(o.created_at) = '$today'
               ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_sql);

// Get today's top products
$top_products_sql = "SELECT p.name, p.category, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price) as total_sales
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     JOIN orders o ON oi.order_id = o.id
                     WHERE DATE(o.created_at) = '$today' AND o.status != 'cancelled'
                     GROUP BY oi.product_id
                     ORDER BY total_quantity DESC
                     LIMIT 10";
$top_products_result = $conn->query($top_products_sql);

// Get payment method breakdown
$payment_breakdown_sql = "SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total
                          FROM orders 
                          WHERE DATE(created_at) = '$today' AND status != 'cancelled'
                          GROUP BY payment_method";
$payment_breakdown_result = $conn->query($payment_breakdown_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Reports - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-header { display: block !important; }
            body { font-size: 12pt; }
            .admin-content { margin-left: 0; }
            .admin-sidebar { display: none; }
        }
        .print-header { display: none; }
        .report-section { margin-bottom: 2rem; page-break-inside: avoid; }
    </style>
</head>
<body class="admin-page">
    <?php include 'header.php'; ?>
    
    <div class="print-header text-center mb-4">
        <h1>Café Online - Daily Report</h1>
        <h3><?php echo date('F j, Y'); ?></h3>
        <hr>
    </div>
    
    <div class="admin-sidebar no-print">
        <div class="sidebar-brand">
            <span class="cafe-icon">☕</span>
            <h4>Café Admin</h4>
            <small>Management Portal</small>
        </div>
        
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action">
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
            <a href="daily_reports.php" class="list-group-item list-group-item-action active">
                <i class="bi bi-file-text"></i> Daily Reports
            </a>
            <?php if($role == 'admin'): ?>
            <a href="manage_users.php" class="list-group-item list-group-item-action">
                <i class="bi bi-people"></i> Manage Users
            </a>
            <a href="inventory_management.php" class="list-group-item list-group-item-action">
                <i class="bi bi-box-seam"></i> Inventory Management
            </a>
            <?php endif; ?>
            <a href="customer_loyalty.php" class="list-group-item list-group-item-action">
                <i class="bi bi-award"></i> Customer Loyalty
            </a>
        </div>
    </div>
    
    <div class="admin-content">
        <div class="admin-content-area">
            <div class="admin-page-header no-print">
                <h1 class="admin-page-title">
                    <i class="bi bi-file-text me-2"></i>Daily Reports - <?php echo date('F j, Y'); ?>
                </h1>
                <button onclick="window.print()" class="btn btn-admin-primary">
                    <i class="bi bi-printer me-2"></i>Print Report
                </button>
            </div>
            
            <!-- Sales Summary -->
            <div class="report-section">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="admin-stats-card">
                            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #007bff, #0056b3); color: white;">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="mb-0"><?php echo $sales_summary['total_orders'] ?? 0; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-stats-card">
                            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: white;">
                                <h5 class="card-title">Total Sales</h5>
                                <h2 class="mb-0">₱<?php echo number_format($sales_summary['total_sales'] ?? 0, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="admin-stats-card">
                            <div class="card-body text-center p-4" style="background: linear-gradient(135deg, #17a2b8, #117a8b); color: white;">
                                <h5 class="card-title">Average Order</h5>
                                <h2 class="mb-0">₱<?php echo number_format($sales_summary['avg_order_value'] ?? 0, 2); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Today's Orders -->
            <div class="report-section">
                <div class="admin-card mb-4">
                    <div class="admin-card-header">
                        <h5><i class="bi bi-list-ul me-2"></i>Today's Orders</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="admin-table table mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($orders_result->num_rows > 0): ?>
                                        <?php while($order = $orders_result->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo $order['username']; ?></td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td><?php echo $order['payment_method']; ?></td>
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
                                            <td colspan="6" class="text-center">No orders today</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Top Products -->
                <div class="col-md-6">
                    <div class="report-section">
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5><i class="bi bi-trophy me-2"></i>Top Products Today</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th>Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($top_products_result->num_rows > 0): ?>
                                                <?php while($product = $top_products_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $product['name']; ?></td>
                                                        <td><?php echo $product['total_quantity']; ?></td>
                                                        <td>₱<?php echo number_format($product['total_sales'], 2); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No sales today</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods -->
                <div class="col-md-6">
                    <div class="report-section">
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5><i class="bi bi-credit-card me-2"></i>Payment Methods</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Method</th>
                                                <th>Orders</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($payment_breakdown_result->num_rows > 0): ?>
                                                <?php while($payment = $payment_breakdown_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $payment['payment_method']; ?></td>
                                                        <td><?php echo $payment['count']; ?></td>
                                                        <td>₱<?php echo number_format($payment['total'], 2); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No payments today</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 print-only">
                <small>Generated on <?php echo date('F j, Y g:i A'); ?></small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
