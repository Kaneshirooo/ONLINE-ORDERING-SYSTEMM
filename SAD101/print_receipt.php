<?php
session_start();
include_once 'db_connect.php';

// Enhanced security and error handling
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'customer';

// Get order details with basic user info
$order_sql = "SELECT o.*, u.username, u.email 
             FROM orders o 
             JOIN users u ON o.user_id = u.id
             WHERE o.id = $order_id";

if ($role == 'customer') {
    $order_sql .= " AND o.user_id = $user_id";
}

$order_result = $conn->query($order_sql);

if (!$order_result || $order_result->num_rows == 0) {
    header("Location: " . ($role == 'customer' ? 'orders.php' : 'manage_orders.php'));
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, p.name, p.category
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_sql);

// Check if loyalty table exists and get loyalty information
$loyalty = null;
$loyalty_table_exists = false;

// Check if customer_loyalty table exists
$check_table = $conn->query("SHOW TABLES LIKE 'customer_loyalty'");
if ($check_table && $check_table->num_rows > 0) {
    $loyalty_table_exists = true;
    $loyalty_sql = "SELECT * FROM customer_loyalty WHERE user_id = " . $order['user_id'];
    $loyalty_result = $conn->query($loyalty_sql);
    if ($loyalty_result && $loyalty_result->num_rows > 0) {
        $loyalty = $loyalty_result->fetch_assoc();
    }
}

// Calculate totals
$subtotal = 0;
$items_array = [];
if ($items_result) {
    while ($item = $items_result->fetch_assoc()) {
        $subtotal += ($item['price'] * $item['quantity']);
        $items_array[] = $item;
    }
}

// Handle discount amount safely
$discount_amount = 0;
if (isset($order['discount_amount']) && is_numeric($order['discount_amount'])) {
    $discount_amount = $order['discount_amount'];
}

// Handle tax amount safely
$tax_amount = 0;
if (isset($order['tax_amount']) && is_numeric($order['tax_amount'])) {
    $tax_amount = $order['tax_amount'];
}

// Enhanced function to get highly specific product images
function getProductImage($name, $category) {
    $name = strtolower(trim($name));
    $category = strtolower(trim($category));
    
    // COFFEE CATEGORY
    if($category == 'coffee' || strpos($name, 'coffee') !== false) {
        if(strpos($name, 'espresso') !== false) {
            return 'https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'latte') !== false) {
            return 'https://images.unsplash.com/photo-1561047029-3000c68339ca?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'cappuccino') !== false) {
            return 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'americano') !== false) {
            return 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'mocha') !== false) {
            return 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop&auto=format';
        }
        return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
    }
    
    // PASTRY CATEGORY
    if($category == 'pastry' || strpos($name, 'pastry') !== false) {
        if(strpos($name, 'croissant') !== false) {
            return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'muffin') !== false) {
            return 'https://images.unsplash.com/photo-1607958996333-41aef7caefaa?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'donut') !== false) {
            return 'https://images.unsplash.com/photo-1551024506-0bccd828d307?w=400&h=400&fit=crop&auto=format';
        }
        if(strpos($name, 'cake') !== false) {
            return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=400&fit=crop&auto=format';
        }
        return 'https://images.unsplash.com/photo-1555507036-ab794f4afe5e?w=400&h=400&fit=crop&auto=format';
    }
    
    // Default fallback
    return 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop&auto=format';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo $order_id; ?> - Etched Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Enhanced print styles */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            
            body { 
                font-size: 12pt; 
                color: #000; 
                background-color: #fff;
                margin: 0;
                padding: 0;
            }
            
            .no-print { 
                display: none !important; 
            }
            
            .container { 
                width: 100%; 
                max-width: 100%; 
                padding: 0; 
                margin: 0; 
            }
            
            .receipt { 
                border: none; 
                box-shadow: none;
                padding: 20px; 
                margin: 0;
                page-break-inside: avoid;
            }
            
            .receipt-header { 
                text-align: center; 
                margin-bottom: 25px; 
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }
            
            .receipt-footer { 
                text-align: center; 
                margin-top: 25px; 
                font-size: 10pt;
                border-top: 1px dashed #000;
                padding-top: 15px;
            }
            
            .table {
                border: 1px solid #000 !important;
            }
            
            .table th, .table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
            }
            
            .table thead th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
            }
            
            .table tfoot th {
                background-color: #e9ecef !important;
                font-weight: bold !important;
            }
            
            .cafe-logo {
                font-size: 24pt;
                font-weight: bold;
                color: #8B4513;
                margin-bottom: 10px;
            }
            
            .receipt-divider {
                border-top: 2px dashed #000;
                margin: 20px 0;
            }
        }
        
        /* Screen styles */
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .cafe-logo {
            font-size: 28px;
            font-weight: bold;
            color: #8B4513;
            margin-bottom: 10px;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px dashed #dee2e6;
            color: #6c757d;
        }
        
        .order-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .total-section {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Print Controls (Hidden when printing) -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h1><i class="bi bi-receipt me-2"></i>Receipt - Order #<?php echo $order_id; ?></h1>
            <div>
                <button type="button" onclick="printReceipt()" class="btn btn-primary me-2">
                    <i class="bi bi-printer me-1"></i> Print Receipt
                </button>
                <button type="button" onclick="downloadPDF()" class="btn btn-success me-2">
                    <i class="bi bi-download me-1"></i> Download PDF
                </button>
                <a href="<?php echo $role == 'customer' ? 'orders.php' : 'manage_orders.php'; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        
        <!-- Receipt Content -->
        <div class="card receipt">
            <div class="card-body">
                <!-- Header -->
                <div class="receipt-header">
                    <div class="cafe-logo">☕ Etched Café</div>
                    <p class="mb-1"><strong>Premium Coffee & Artisan Pastries</strong></p>
                    <p class="mb-1">123 Coffee Street, Café City, Philippines</p>
                    <p class="mb-1">📞 (123) 456-7890 | 📧 info@etchedcafe.com</p>
                    <p class="mb-0">🌐 www.etchedcafe.com</p>
                </div>
                
                <!-- Order Information -->
                <div class="order-info">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-receipt-cutoff me-2"></i>Order Details</h5>
                            <p class="mb-1"><strong>Order #:</strong> <?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                            <p class="mb-1"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p class="mb-0"><strong>Time:</strong> <?php echo date('g:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-person me-2"></i>Customer Information</h5>
                            <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <?php if($loyalty): ?>
                                <p class="mb-0">
                                    <strong>Loyalty:</strong> 
                                    <span class="badge bg-warning text-dark">
                                        <?php echo ucfirst($loyalty['tier']); ?> (<?php echo $loyalty['points']; ?> pts)
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <?php if (count($items_array) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Item</th>
                                <th style="width: 15%;">Category</th>
                                <th style="width: 15%;" class="text-end">Unit Price</th>
                                <th style="width: 10%;" class="text-center">Qty</th>
                                <th style="width: 20%;" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items_array as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    <?php if($item['category'] == 'coffee'): ?>
                                        <small class="text-muted d-block">☕ Premium Coffee</small>
                                    <?php elseif($item['category'] == 'pastry'): ?>
                                        <small class="text-muted d-block">🥐 Fresh Pastry</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $item['category'] == 'coffee' ? 'primary' : 'warning'; ?>">
                                        <?php echo ucfirst($item['category']); ?>
                                    </span>
                                </td>
                                <td class="text-end">₱<?php echo number_format($item['price'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?php echo $item['quantity']; ?></span>
                                </td>
                                <td class="text-end"><strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <!-- Totals Section -->
                <div class="total-section">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end">₱<?php echo number_format($subtotal, 2); ?></td>
                                </tr>
                                <?php if($discount_amount > 0): ?>
                                <tr class="table-success">
                                    <td><strong>Loyalty Discount:</strong></td>
                                    <td class="text-end">-₱<?php echo number_format($discount_amount, 2); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($tax_amount > 0): ?>
                                <tr>
                                    <td><strong>Tax (12%):</strong></td>
                                    <td class="text-end">₱<?php echo number_format($tax_amount, 2); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="table-warning">
                                    <td><strong style="font-size: 1.1em;">TOTAL:</strong></td>
                                    <td class="text-end"><strong style="font-size: 1.2em;">₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Payment & Status Information -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-credit-card me-2"></i>Payment Information</h6>
                        <p class="mb-1">
                            <strong>Method:</strong> 
                            <span class="badge bg-info"><?php echo ucfirst($order['payment_method']); ?></span>
                        </p>
                        <p class="mb-0">
                            <strong>Status:</strong> 
                            <span class="badge bg-<?php 
                                switch($order['status']) {
                                    case 'pending': echo 'warning'; break;
                                    case 'processing': echo 'info'; break;
                                    case 'completed': echo 'success'; break;
                                    case 'cancelled': echo 'danger'; break;
                                    default: echo 'secondary';
                                }
                            ?>"><?php echo ucfirst($order['status']); ?></span>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if($loyalty): ?>
                            <h6><i class="bi bi-award me-2"></i>Loyalty Rewards</h6>
                            <p class="mb-1"><strong>Points Earned:</strong> +<?php echo floor($order['total_amount']); ?> pts</p>
                            <p class="mb-0"><strong>Total Points:</strong> <?php echo $loyalty['points']; ?> pts</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="receipt-footer">
                    <h5>🙏 Thank you for choosing Etched Café!</h5>
                    <p class="mb-1">We appreciate your business and hope you enjoyed your order.</p>
                    <p class="mb-1">📱 Follow us on social media for updates and special offers</p>
                    <p class="mb-1">⭐ Rate your experience and earn bonus loyalty points!</p>
                    <p class="mb-0"><small>Receipt generated on <?php echo date('F j, Y g:i A'); ?></small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printReceipt() {
            // Hide any alerts or modals before printing
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => alert.style.display = 'none');
            
            // Print the page
            window.print();
            
            // Show alerts again after printing
            setTimeout(() => {
                alerts.forEach(alert => alert.style.display = 'block');
            }, 1000);
        }
        
        function downloadPDF() {
            // Simple PDF download using browser's print to PDF
            alert('To download as PDF:\n1. Click "Print Receipt"\n2. Choose "Save as PDF" as destination\n3. Click Save');
            printReceipt();
        }
        
        // Auto-focus print button for keyboard users
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('button[onclick="printReceipt()"]');
            if (printBtn) {
                printBtn.focus();
            }
        });
        
        // Keyboard shortcut for printing (Ctrl+P)
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printReceipt();
            }
        });
    </script>
</body>
</html>