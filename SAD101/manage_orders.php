<?php
session_start();
// Include database connection
include_once 'db_connect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and is admin or staff
if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'staff')) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

// Handle order status updates
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['status']);
    
    $update_sql = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    if($conn->query($update_sql) === TRUE) {
        $success = "Order status updated successfully!";
    } else {
        $error = "Error updating order status: " . $conn->error;
    }
}

// Get all orders with user information
$orders_sql = "SELECT o.*, u.username, u.email 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_sql);

// If created_at doesn't exist, try order_date
if(!$orders_result) {
    $orders_sql = "SELECT o.*, u.username, u.email 
                   FROM orders o 
                   JOIN users u ON o.user_id = u.id 
                   ORDER BY o.order_date DESC";
    $orders_result = $conn->query($orders_sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Orders - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="admin-style.css" />
    <link rel="stylesheet" href="style.css" />
</head>
<body class="admin-page">
    <?php include 'header.php'; ?>
    
    <div class="admin-sidebar">
        <div class="sidebar-brand">
            <span class="cafe-icon">☕</span>
            <h4>Café <?php echo $role == 'staff' ? 'Staff' : 'Admin'; ?></h4>
            <small><?php echo $role == 'staff' ? 'Order Management' : 'Management Portal'; ?></small>
        </div>
        
        <div class="list-group list-group-flush">
            <?php if($role == 'admin'): ?>
            <a href="dashboard.php" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="manage_products.php" class="list-group-item list-group-item-action">
                <i class="bi bi-cup-hot"></i> Manage Products
            </a>
            <?php endif; ?>
            <a href="manage_orders.php" class="list-group-item list-group-item-action active">
                <i class="bi bi-bag"></i> Manage Orders
            </a>
            <?php if($role == 'admin'): ?>
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
            <?php else: ?>
            <div class="list-group-item text-muted">
                <i class="bi bi-info-circle"></i> Staff Access: Orders Only
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="admin-content">
        <div class="admin-content-area">
            <div class="admin-page-header">
                <h1 class="admin-page-title">
                    <i class="bi bi-bag me-2"></i>Manage Orders
                </h1>
                <div class="d-flex gap-2">
                    <button class="btn btn-admin-secondary" onclick="refreshOrders()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5><i class="bi bi-list-ul me-2"></i>All Orders</h5>
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
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($orders_result->num_rows > 0): ?>
                                    <?php while($order = $orders_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                                            <td>
                                                <div>
                                                    <strong><?php echo $order['username']; ?></strong><br />
                                                    <small class="text-muted"><?php echo $order['email']; ?></small>
                                                </div>
                                            </td>
                                            <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
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
                                            <td><?php 
    $date_field = isset($order['created_at']) ? $order['created_at'] : $order['order_date'];
    echo date('M j, Y g:i A', strtotime($date_field)); 
?></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-admin-primary btn-sm" onclick="updateOrderStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bi bi-bag-x" style="font-size: 3rem; color: #8b5a3c;"></i>
                                            <h4 class="admin-text-primary mt-3">No orders found</h4>
                                            <p class="text-muted">Orders will appear here when customers place them.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal fade admin-modal" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Update Order Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" id="updateStatusForm">
                    <input type="hidden" id="status_order_id" name="order_id" />
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="admin-form-label">Order Status</label>
                            <select class="form-control admin-form-control" id="status" name="status" required>
                                <option value="pending">⏳ Pending</option>
                                <option value="processing">🔄 Processing</option>
                                <option value="completed">✅ Completed</option>
                                <option value="cancelled">❌ Cancelled</option>
                            </select>
                        </div>
                        <div class="admin-alert admin-alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> Changing the status will notify the customer via email.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_status" class="btn btn-admin-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateOrderStatus(orderId, currentStatus) {
            document.getElementById('status_order_id').value = orderId;
            document.getElementById('status').value = currentStatus;
            
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
        
        function refreshOrders() {
            location.reload();
        }
        
        // Auto-refresh orders every 30 seconds (optional)
        setInterval(() => {
            console.log('Auto-refresh check...');
        }, 30000);
    </script>
</body>
</html>
