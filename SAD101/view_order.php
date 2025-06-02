<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Check if order ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . ($role == 'customer' ? 'orders.php' : 'manage_orders.php'));
    exit();
}

$order_id = $_GET['id'];

// Get order details
$order_sql = "SELECT o.*, u.username, u.email 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             WHERE o.id = $order_id";

// If customer, only show their own orders
if($role == 'customer') {
    $order_sql .= " AND o.user_id = $user_id";
}

$order_result = $conn->query($order_sql);

if($order_result->num_rows == 0) {
    header("Location: " . ($role == 'customer' ? 'orders.php' : 'manage_orders.php'));
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, p.name, p.image, p.category 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_sql);

// Get loyalty info if available
$loyalty_sql = "SELECT * FROM customer_loyalty WHERE user_id = " . $order['user_id'];
$loyalty_result = $conn->query($loyalty_sql);
$loyalty = $loyalty_result->num_rows > 0 ? $loyalty_result->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order_id; ?> - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Order #<?php echo $order_id; ?></h1>
            <div>
                <a href="print_receipt.php?id=<?php echo $order_id; ?>" class="btn btn-primary me-2" target="_blank" onclick="return checkPrintReceipt(<?php echo $order_id; ?>)">
                    <i class="bi bi-printer"></i> Print Receipt
                </a>
                <a href="<?php echo $role == 'customer' ? 'orders.php' : 'manage_orders.php'; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to <?php echo $role == 'customer' ? 'My Orders' : 'Manage Orders'; ?>
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $subtotal = 0;
                                    while($item = $items_result->fetch_assoc()): 
                                        $item_subtotal = $item['price'] * $item['quantity'];
                                        $subtotal += $item_subtotal;
                                        $category_symbol = $item["category"] == "coffee" ? "☕" : "🥐";
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="order-item-image me-3">
                                                    <span><?php echo $item['name']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo ucfirst($item['category']); ?> <?php echo $category_symbol; ?></td>
                                            <td>₱<?php echo $item['price']; ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₱<?php echo number_format($item_subtotal, 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">Subtotal:</td>
                                        <td>₱<?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                                    <?php if(isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                    <tr class="text-success">
                                        <td colspan="4" class="text-end">
                                            <i class="bi bi-award"></i> Loyalty Discount:
                                        </td>
                                        <td>-₱<?php echo number_format($order['discount_amount'], 2); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Order Date:</span>
                                <span><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Status:</span>
                                <span class="badge bg-<?php 
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
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Payment Method:</span>
                                <span>
                                    <?php if($order['payment_method'] == 'Online Payment'): ?>
                                        <span class="badge bg-primary"><?php echo $order['payment_method']; ?></span>
                                    <?php else: ?>
                                        <?php echo $order['payment_method']; ?>
                                    <?php endif; ?>
                                </span>
                            </li>
                            <?php if($loyalty): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span>Loyalty Tier:</span>
                                    <span class="loyalty-badge loyalty-<?php echo $loyalty['tier']; ?>">
                                        <?php echo ucfirst($loyalty['tier']); ?>
                                    </span>
                                </div>
                                <div class="small text-muted mt-1">
                                    Current Points: <?php echo $loyalty['points']; ?>
                                </div>
                            </li>
                            <?php endif; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total Amount:</span>
                                <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php if($role == 'admin' || $role == 'staff'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Name:</strong> <?php echo $order['username']; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Email:</strong> <?php echo $order['email']; ?>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Update Status</h5>
                    </div>
                    <div class="card-body">
                        <form action="manage_orders.php" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <div class="mb-3">
                                <select class="form-select" name="status">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" name="update_status" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function checkPrintReceipt(orderId) {
    if (!orderId) {
        alert("Invalid order ID. Cannot print receipt.");
        return false;
    }
    return true;
}
</script>
</body>
</html>
