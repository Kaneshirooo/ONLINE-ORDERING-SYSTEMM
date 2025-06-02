<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Set default date range (last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

// Handle date range filter
if(isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Get sales data
$sales_sql = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_sales, COUNT(*) as order_count 
             FROM orders 
             WHERE created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
             AND status != 'cancelled'
             GROUP BY DATE(created_at)
             ORDER BY order_date ASC";
$sales_result = $conn->query($sales_sql);

$sales_data = [];
$total_sales = 0;
$total_orders = 0;

if($sales_result->num_rows > 0) {
    while($row = $sales_result->fetch_assoc()) {
        $sales_data[] = $row;
        $total_sales += $row['daily_sales'];
        $total_orders += $row['order_count'];
    }
}

// Get top selling products
$top_products_sql = "SELECT p.name, p.category, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price) as total_sales
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON oi.order_id = o.id
                    WHERE o.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                    AND o.status != 'cancelled'
                    GROUP BY oi.product_id
                    ORDER BY total_quantity DESC
                    LIMIT 10";
$top_products_result = $conn->query($top_products_sql);

// Get category sales
$category_sales_sql = "SELECT p.category, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price) as total_sales
                      FROM order_items oi
                      JOIN products p ON oi.product_id = p.id
                      JOIN orders o ON oi.order_id = o.id
                      WHERE o.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                      AND o.status != 'cancelled'
                      GROUP BY p.category
                      ORDER BY total_sales DESC";
$category_sales_result = $conn->query($category_sales_sql);

// Get payment method breakdown
$payment_method_sql = "SELECT payment_method, COUNT(*) as order_count, SUM(total_amount) as total_sales
                      FROM orders
                      WHERE created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                      AND status != 'cancelled'
                      GROUP BY payment_method
                      ORDER BY total_sales DESC";
$payment_method_result = $conn->query($payment_method_sql);

// Calculate average order value
$avg_order_value = $total_orders > 0 ? $total_sales / $total_orders : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="sales_analytics.php" class="list-group-item list-group-item-action active">
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
                    <a href="customer_loyalty.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-award me-2"></i> Customer Loyalty
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Sales Analytics</h1>
                    <button onclick="window.print()" class="btn btn-outline-secondary no-print">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>
                
                <!-- Date Range Filter -->
                <div class="card mb-4 no-print">
                    <div class="card-body">
                        <form action="sales_analytics.php" method="get" class="row g-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Sales Summary -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Sales</h5>
                                <h2 class="mb-0">₱<?php echo number_format($total_sales, 2); ?></h2>
                                <small>From <?php echo date('M d, Y', strtotime($start_date)); ?> to <?php echo date('M d, Y', strtotime($end_date)); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <h2 class="mb-0"><?php echo $total_orders; ?></h2>
                                <small>From <?php echo date('M d, Y', strtotime($start_date)); ?> to <?php echo date('M d, Y', strtotime($end_date)); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Average Order Value</h5>
                                <h2 class="mb-0">₱<?php echo number_format($avg_order_value, 2); ?></h2>
                                <small>From <?php echo date('M d, Y', strtotime($start_date)); ?> to <?php echo date('M d, Y', strtotime($end_date)); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sales Chart -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Daily Sales</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Top Selling Products -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Top Selling Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Category</th>
                                                <th>Quantity</th>
                                                <th>Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($top_products_result->num_rows > 0): ?>
                                                <?php while($product = $top_products_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $product['name']; ?></td>
                                                        <td>
                                                            <?php if($product['category'] == 'coffee'): ?>
                                                                <span class="badge bg-secondary"><i class="bi bi-cup-hot"></i> Coffee</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark"><i class="bi bi-pie-chart"></i> Pastry</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $product['total_quantity']; ?></td>
                                                        <td>₱<?php echo number_format($product['total_sales'], 2); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">No data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Sales -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Sales by Category</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="categoryChart" height="200"></canvas>
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Quantity</th>
                                                <th>Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($category_sales_result->num_rows > 0): ?>
                                                <?php while($category = $category_sales_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if($category['category'] == 'coffee'): ?>
                                                                <span class="badge bg-secondary"><i class="bi bi-cup-hot"></i> Coffee</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark"><i class="bi bi-pie-chart"></i> Pastry</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo $category['total_quantity']; ?></td>
                                                        <td>₱<?php echo number_format($category['total_sales'], 2); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
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
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="paymentChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th>Orders</th>
                                                <th>Sales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($payment_method_result->num_rows > 0): ?>
                                                <?php while($payment = $payment_method_result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $payment['payment_method']; ?></td>
                                                        <td><?php echo $payment['order_count']; ?></td>
                                                        <td>₱<?php echo number_format($payment['total_sales'], 2); ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">No data available</td>
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
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php 
                    foreach($sales_data as $data) {
                        echo "'" . date('M d', strtotime($data['order_date'])) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Daily Sales (₱)',
                    data: [
                        <?php 
                        foreach($sales_data as $data) {
                            echo $data['daily_sales'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }, {
                    label: 'Order Count',
                    data: [
                        <?php 
                        foreach($sales_data as $data) {
                            echo $data['order_count'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales (₱)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Order Count'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
        
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'pie',
            data: {
                labels: [
                    <?php 
                    $category_sales_result->data_seek(0);
                    while($category = $category_sales_result->fetch_assoc()) {
                        echo "'" . ucfirst($category['category']) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                        $category_sales_result->data_seek(0);
                        while($category = $category_sales_result->fetch_assoc()) {
                            echo $category['total_sales'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(111, 78, 55, 0.7)', // Coffee brown
                        'rgba(245, 176, 65, 0.7)'  // Pastry gold
                    ],
                    borderColor: [
                        'rgba(111, 78, 55, 1)',
                        'rgba(245, 176, 65, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '₱' + new Intl.NumberFormat().format(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Payment Method Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php 
                    $payment_method_result->data_seek(0);
                    while($payment = $payment_method_result->fetch_assoc()) {
                        echo "'" . $payment['payment_method'] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php 
                        $payment_method_result->data_seek(0);
                        while($payment = $payment_method_result->fetch_assoc()) {
                            echo $payment['total_sales'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '₱' + new Intl.NumberFormat().format(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
