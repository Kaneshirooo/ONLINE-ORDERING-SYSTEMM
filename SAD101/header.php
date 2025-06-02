<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection for cart count
if(isset($_SESSION['user_id'])) {
    include_once 'db_connect.php';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Etched Cafe</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="products.php?category=coffee">Coffee</a></li>
                        <li><a class="dropdown-item" href="products.php?category=pastries">Pastries</a></li>
                    </ul>
                </li>
                <?php if(isset($_SESSION['user_id']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff')): ?>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php echo $_SESSION['role'] == 'staff' ? 'Staff Panel' : 'Management'; ?>
    </a>
    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
        <?php if($_SESSION['role'] == 'staff'): ?>
            <!-- Staff only sees Orders -->
            <li><a class="dropdown-item" href="manage_orders.php"><i class="bi bi-bag me-2"></i>Manage Orders</a></li>
        <?php elseif($_SESSION['role'] == 'admin'): ?>
            <!-- Admin sees everything -->
            <li><a class="dropdown-item" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
            <li><a class="dropdown-item" href="manage_products.php"><i class="bi bi-cup-hot me-2"></i>Products</a></li>
            <li><a class="dropdown-item" href="manage_orders.php"><i class="bi bi-bag me-2"></i>Orders</a></li>
            <li><a class="dropdown-item" href="sales_analytics.php"><i class="bi bi-graph-up me-2"></i>Sales Analytics</a></li>
            <li><a class="dropdown-item" href="daily_reports.php"><i class="bi bi-file-text me-2"></i>Daily Reports</a></li>
            <li><a class="dropdown-item" href="manage_users.php"><i class="bi bi-people me-2"></i>Users</a></li>
            <li><a class="dropdown-item" href="inventory_management.php"><i class="bi bi-box-seam me-2"></i>Inventory</a></li>
            <li><a class="dropdown-item" href="customer_loyalty.php"><i class="bi bi-award me-2"></i>Customer Loyalty</a></li>
        <?php endif; ?>
    </ul>
</li>
<?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user_id']) && isset($conn)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Cart
                            <?php
                            $user_id = $_SESSION['user_id'];
                            $cart_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
                            $cart_result = $conn->query($cart_query);
                            if($cart_result) {
                                $cart_row = $cart_result->fetch_assoc();
                                if($cart_row['total'] > 0) {
                                    echo '<span class="badge bg-danger">' . $cart_row['total'] . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                <?php elseif(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="bi bi-cart"></i> Cart
                        </a>
                    </li>
                <?php endif; ?>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
