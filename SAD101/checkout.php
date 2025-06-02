<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details for auto-fill
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Get cart items
$cart_sql = "SELECT c.*, p.name, p.price, p.image, p.category 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = $user_id";
$cart_result = $conn->query($cart_sql);

if($cart_result->num_rows == 0) {
    header("Location: cart.php");
    exit();
}

$total = 0;
$cart_items = [];
while($item = $cart_result->fetch_assoc()) {
    $cart_items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

// Get loyalty info for discount calculation
$loyalty_sql = "SELECT * FROM customer_loyalty WHERE user_id = $user_id";
$loyalty_result = $conn->query($loyalty_sql);
$loyalty = $loyalty_result->num_rows > 0 ? $loyalty_result->fetch_assoc() : null;

$discount = 0;
$discount_percentage = 0;

if($loyalty) {
    switch($loyalty['tier']) {
        case 'bronze': $discount_percentage = 5; break;
        case 'silver': $discount_percentage = 10; break;
        case 'gold': $discount_percentage = 15; break;
        case 'platinum': $discount_percentage = 20; break;
    }
    $discount = ($total * $discount_percentage) / 100;
}

$final_total = $total - $discount;

$error = '';
$success = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_email = $conn->real_escape_string($_POST['customer_email']);
    $customer_phone = $conn->real_escape_string($_POST['customer_phone']);
    
    // Validate payment method specific fields
    if($payment_method == 'Online Payment') {
        $card_number = $_POST['card_number'];
        $exp_date = $_POST['exp_date'];
        $cvv = $_POST['cvv'];
        
        if(empty($card_number) || empty($exp_date) || empty($cvv)) {
            $error = "Please fill in all payment details";
        }
    }
    
    if(empty($error)) {
        // Create order
        $order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, discount_amount) 
                     VALUES ($user_id, $final_total, '$payment_method', $discount)";
        
        if($conn->query($order_sql) === TRUE) {
            $order_id = $conn->insert_id;
            
            // Add order items
            foreach($cart_items as $item) {
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})";
                $conn->query($item_sql);
            }
            
            // Add loyalty points (1 point per peso spent)
            $points_earned = floor($final_total);
            if($loyalty) {
                $new_points = $loyalty['points'] + $points_earned;
                $new_tier = 'bronze';
                if($new_points >= 1000) $new_tier = 'platinum';
                elseif($new_points >= 500) $new_tier = 'gold';
                elseif($new_points >= 200) $new_tier = 'silver';
                
                $loyalty_update_sql = "UPDATE customer_loyalty SET points = $new_points, tier = '$new_tier' WHERE user_id = $user_id";
                $conn->query($loyalty_update_sql);
            } else {
                $tier = $points_earned >= 200 ? 'silver' : 'bronze';
                $loyalty_insert_sql = "INSERT INTO customer_loyalty (user_id, points, tier) VALUES ($user_id, $points_earned, '$tier')";
                $conn->query($loyalty_insert_sql);
            }
            
            // Add to loyalty history
            $history_sql = "INSERT INTO loyalty_history (user_id, points, reason) VALUES ($user_id, $points_earned, 'Order #$order_id')";
            $conn->query($history_sql);
            
            // Clear cart
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = $user_id";
            $conn->query($clear_cart_sql);
            
            header("Location: print_receipt.php?id=$order_id");
            exit();
        } else {
            $error = "Error creating order: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-container {
            background: linear-gradient(135deg, #f8f5f2 0%, #e8e0d7 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .checkout-card {
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }
        .checkout-header {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-control:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 0 0.25rem rgba(111, 78, 55, 0.25);
        }
        .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-option:hover {
            border-color: #6f4e37;
            background-color: #f8f5f2;
        }
        .payment-option.active {
            border-color: #6f4e37;
            background-color: #f5ebe0;
        }
        .order-summary {
            background-color: #f8f5f2;
            border-radius: 15px;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="checkout-container">
        <div class="container">
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card checkout-card">
                        <div class="checkout-header">
                            <h2><i class="bi bi-credit-card me-2"></i>Secure Checkout</h2>
                            <p class="mb-0">Complete your order safely and securely</p>
                        </div>
                        
                        <div class="card-body p-4">
                            <form action="checkout.php" method="post" id="checkoutForm">
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Customer Information (Auto-filled) -->
                                        <div class="mb-4">
                                            <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Customer Information</h5>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="customer_name" class="form-label">Full Name</label>
                                                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                                           value="<?php echo $user['username']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="customer_email" class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                                           value="<?php echo $user['email']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="customer_phone" class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                                           placeholder="Enter your phone number" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Payment Method -->
                                        <div class="mb-4">
                                            <h5 class="mb-3"><i class="bi bi-credit-card me-2"></i>Payment Method</h5>
                                            
                                            <div class="payment-option" data-payment="Cash on Delivery">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                                                    <label class="form-check-label" for="cod">
                                                        <i class="bi bi-cash-coin me-2"></i>
                                                        <strong>Cash on Delivery</strong>
                                                        <small class="d-block text-muted">Pay when you receive your order</small>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="payment-option" data-payment="Pay at Pickup">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="pickup" value="Pay at Pickup">
                                                    <label class="form-check-label" for="pickup">
                                                        <i class="bi bi-shop me-2"></i>
                                                        <strong>Pay at Pickup</strong>
                                                        <small class="d-block text-muted">Pay when you collect your order</small>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="payment-option" data-payment="Online Payment">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="online" value="Online Payment">
                                                    <label class="form-check-label" for="online">
                                                        <i class="bi bi-credit-card me-2"></i>
                                                        <strong>Credit/Debit Card</strong>
                                                        <small class="d-block text-muted">Pay securely with your card</small>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Online Payment Details -->
                                            <div id="onlinePaymentDetails" class="mt-3" style="display: none;">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6><i class="bi bi-shield-lock me-2"></i>Secure Payment Details</h6>
                                                        <div class="row">
                                                            <div class="col-md-8 mb-3">
                                                                <label for="card_number" class="form-label">Card Number</label>
                                                                <input type="text" class="form-control" id="card_number" name="card_number" 
                                                                       placeholder="1234 5678 9012 3456" maxlength="19">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="cvv" class="form-label">CVV</label>
                                                                <input type="text" class="form-control" id="cvv" name="cvv" 
                                                                       placeholder="123" maxlength="4">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="exp_date" class="form-label">Expiry Date</label>
                                                                <input type="text" class="form-control" id="exp_date" name="exp_date" 
                                                                       placeholder="MM/YY" maxlength="5">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <!-- Order Summary -->
                                        <div class="order-summary">
                                            <h5 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
                                            
                                            <div class="summary-items">
                                                <?php foreach($cart_items as $item): ?>
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" 
                                                                 class="cart-item-image me-2">
                                                            <div>
                                                                <small class="fw-bold"><?php echo $item['name']; ?></small>
                                                                <br><small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            
                                            <hr>
                                            
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Subtotal:</span>
                                                <span>₱<?php echo number_format($total, 2); ?></span>
                                            </div>
                                            
                                            <?php if($discount > 0): ?>
                                                <div class="d-flex justify-content-between mb-2 text-success">
                                                    <span><i class="bi bi-award me-1"></i>Loyalty Discount (<?php echo $discount_percentage; ?>%):</span>
                                                    <span>-₱<?php echo number_format($discount, 2); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <hr>
                                            
                                            <div class="d-flex justify-content-between mb-3">
                                                <strong>Total:</strong>
                                                <strong class="text-primary">₱<?php echo number_format($final_total, 2); ?></strong>
                                            </div>
                                            
                                            <?php if($loyalty): ?>
                                                <div class="alert alert-info">
                                                    <small>
                                                        <i class="bi bi-star-fill me-1"></i>
                                                        You'll earn <?php echo floor($final_total); ?> loyalty points!
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                                <i class="bi bi-lock-fill me-2"></i>Place Order
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove active class from all options
                document.querySelectorAll('.payment-option').forEach(option => {
                    option.classList.remove('active');
                });
                
                // Add active class to selected option
                this.closest('.payment-option').classList.add('active');
                
                // Show/hide online payment details
                const onlineDetails = document.getElementById('onlinePaymentDetails');
                if(this.value === 'Online Payment') {
                    onlineDetails.style.display = 'block';
                    // Make fields required
                    document.getElementById('card_number').required = true;
                    document.getElementById('exp_date').required = true;
                    document.getElementById('cvv').required = true;
                } else {
                    onlineDetails.style.display = 'none';
                    // Remove required attribute
                    document.getElementById('card_number').required = false;
                    document.getElementById('exp_date').required = false;
                    document.getElementById('cvv').required = false;
                }
            });
        });
        
        // Set initial active state
        document.querySelector('input[name="payment_method"]:checked').closest('.payment-option').classList.add('active');
        
        // Format card number
        document.getElementById('card_number').addEventListener('input', function() {
            let value = this.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            this.value = formattedValue;
        });
        
        // Format expiry date
        document.getElementById('exp_date').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value;
        });
        
        // CVV numbers only
        document.getElementById('cvv').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
