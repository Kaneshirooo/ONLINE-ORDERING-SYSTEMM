<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user information
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Handle profile update
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Check if username or email already exists (excluding current user)
    $check_sql = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != $user_id";
    $check_result = $conn->query($check_sql);
    
    if($check_result->num_rows > 0) {
        $error = "Username or email already exists!";
    } else {
        $update_sql = "UPDATE users SET username = '$username', email = '$email', phone = '$phone', address = '$address' WHERE id = $user_id";
        
        if($conn->query($update_sql)) {
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
            // Refresh user data
            $user_result = $conn->query($user_sql);
            $user = $user_result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    }
}

// Handle password change
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    if(password_verify($current_password, $user['password'])) {
        if($new_password === $confirm_password) {
            if(strlen($new_password) >= 6) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $password_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
                
                if($conn->query($password_sql)) {
                    $success = "Password changed successfully!";
                } else {
                    $error = "Error changing password: " . $conn->error;
                }
            } else {
                $error = "Password must be at least 6 characters long!";
            }
        } else {
            $error = "New passwords do not match!";
        }
    } else {
        $error = "Current password is incorrect!";
    }
}

// Get user's order history
$orders_sql = "SELECT o.*, COUNT(oi.id) as item_count 
               FROM orders o 
               LEFT JOIN order_items oi ON o.id = oi.order_id 
               WHERE o.user_id = $user_id 
               GROUP BY o.id 
               ORDER BY o.created_at DESC 
               LIMIT 5";
$orders_result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f5f2 0%, #e8e0d7 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 50%, #8b5a3c 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(67, 40, 24, 0.15);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6f4e37, #432818);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 0 0.25rem rgba(111, 78, 55, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #6f4e37, #432818);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #8b5a3c, #6f4e37);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            border: 2px solid #6f4e37;
            color: #6f4e37;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: #6f4e37;
            border-color: #6f4e37;
            color: white;
            transform: translateY(-2px);
        }
        
        .order-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 5px 15px rgba(67, 40, 24, 0.1);
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6f4e37;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
        }
        
        .nav-tabs .nav-link.active {
            background: #6f4e37;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">👤 My Profile</h1>
                    <p class="mb-0 opacity-75">Manage your account settings and view your order history</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Profile Tabs -->
        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                    <i class="bi bi-person me-2"></i>Profile Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                    <i class="bi bi-lock me-2"></i>Change Password
                </button>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                    <i class="bi bi-bag me-2"></i>Order History
                </button>
            </li> -->
        </ul>
        
        <div class="tab-content" id="profileTabsContent">
            <!-- Profile Information Tab -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="profile-card">
                    <h4 class="mb-4"><i class="bi bi-person-circle me-2"></i>Profile Information</h4>
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Account Type</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Tab -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <div class="profile-card">
                    <h4 class="mb-4"><i class="bi bi-shield-lock me-2"></i>Change Password</h4>
                    <form method="post">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text">Password must be at least 6 characters long.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <i class="bi bi-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
            
           Order History Tab
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="profile-card">
                    <h4 class="mb-4"><i class="bi bi-clock-history me-2"></i>Recent Orders</h4>
                    <?php if($orders_result->num_rows > 0): ?>
                        <?php while($order = $orders_result->fetch_assoc()): ?>
                            <div class="order-card">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <strong>Order #<?php echo $order['id']; ?></strong>
                                        <br><small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></small>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="status-badge bg-<?php 
                                            switch($order['status']) {
                                                case 'pending': echo 'warning'; break;
                                                case 'processing': echo 'info'; break;
                                                case 'completed': echo 'success'; break;
                                                case 'cancelled': echo 'danger'; break;
                                                default: echo 'secondary';
                                            }
                                        ?> text-white">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted"><?php echo $order['item_count']; ?> item(s)</small>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <div class="text-center mt-3">
                            <a href="order_history.php" class="btn btn-outline-primary">
                                <i class="bi bi-list me-2"></i>View All Orders
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bag" style="font-size: 3rem; color: #8b5a3c;"></i>
                            <h5 class="mt-3">No orders yet</h5>
                            <p class="text-muted">Start shopping to see your order history here!</p>
                            <a href="products.php" class="btn btn-primary">
                                <i class="bi bi-cup-hot me-2"></i>Browse Menu
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const passwordForm = document.querySelector('form[method="post"]:has([name="change_password"])');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        alert('New passwords do not match!');
                    }
                    
                    if (newPassword.length < 6) {
                        e.preventDefault();
                        alert('Password must be at least 6 characters long!');
                    }
                });
            }
        });
    </script>
</body>
</html>
