<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get orders
$orders_sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders_result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container mt-5">
        <h1>My Orders</h1>
        
        <?php if($orders_result->num_rows > 0): ?>
            <div class="row">
                <?php while($order = $orders_result->fetch_assoc()): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Order #<?php echo $order['id']; ?></h5>
                                    <small class="text-muted">Placed on <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></small>
                                </div>
                                <div>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="print_receipt.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="bi bi-printer"></i> Print
                                    </a>
                                </div>
                                <div>
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
                                </div>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get order items
                                $items_sql = "SELECT oi.*, p.name, p.image, p.category 
                                             FROM order_items oi 
                                             JOIN products p ON oi.product_id = p.id 
                                             WHERE oi.order_id = " . $order['id'];
                                $items_result = $conn->query($items_sql);
                                
                                // Calculate subtotal
                                $subtotal = 0;
                                $items_array = [];
                                while($item = $items_result->fetch_assoc()) {
                                    $subtotal += ($item['price'] * $item['quantity']);
                                    $items_array[] = $item;
                                }
                                
                                // Get discount amount
                                $discount_amount = isset($order['discount_amount']) ? $order['discount_amount'] : 0;
                                ?>
                                
                                <div class="table-responsive">
                                    <table class="table table-borderless">
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
                                            <?php foreach($items_array as $item): 
                                                $item_subtotal = $item['price'] * $item['quantity'];
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
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <?php if($subtotal != $order['total_amount']): ?>
                                                <tr>
                                                    <td colspan="4" class="text-end">Subtotal:</td>
                                                    <td>₱<?php echo number_format($subtotal, 2); ?></td>
                                                </tr>
                                                <?php if($discount_amount > 0): ?>
                                                <tr class="text-success">
                                                    <td colspan="4" class="text-end">
                                                        <i class="bi bi-award"></i> Loyalty Discount:
                                                    </td>
                                                    <td>-₱<?php echo number_format($discount_amount, 2); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                                <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end">Payment Method:</td>
                                                <td><?php echo $order['payment_method']; ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-bag" style="font-size: 5rem;"></i>
                <h3 class="mt-3">No orders yet</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn btn-primary mt-3">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
