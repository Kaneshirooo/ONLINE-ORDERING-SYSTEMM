<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Don't allow admin to delete themselves
    if($user_id != $_SESSION['user_id']) {
        $delete_sql = "DELETE FROM users WHERE id = $user_id";
        
        if($conn->query($delete_sql) === TRUE) {
            $success_message = "User deleted successfully";
        } else {
            $error_message = "Error deleting user: " . $conn->error;
        }
    } else {
        $error_message = "You cannot delete your own account";
    }
}

// Handle role update
if(isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = intval($_POST['user_id']);
    $role = $conn->real_escape_string($_POST['role']);
    
    // Don't allow admin to change their own role
    if($user_id != $_SESSION['user_id']) {
        $update_sql = "UPDATE users SET role = '$role' WHERE id = $user_id";
        
        if($conn->query($update_sql) === TRUE) {
            $success_message = "User role updated successfully";
        } else {
            $error_message = "Error updating user role: " . $conn->error;
        }
    } else {
        $error_message = "You cannot change your own role";
    }
}

// Get users with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

$role_filter = isset($_GET['role']) && $_GET['role'] != 'all' ? $_GET['role'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query
$sql = "SELECT * FROM users WHERE 1=1";

if(!empty($role_filter)) {
    $sql .= " AND role = '$role_filter'";
}

if(!empty($search)) {
    $sql .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}

$sql .= " ORDER BY id ASC LIMIT $offset, $items_per_page";
$result = $conn->query($sql);

// Get total users for pagination
$count_sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";

if(!empty($role_filter)) {
    $count_sql .= " AND role = '$role_filter'";
}

if(!empty($search)) {
    $count_sql .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}

$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$total_users = $count_row['total'];
$total_pages = ceil($total_users / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Café Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
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
                    <a href="manage_users.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-people me-2"></i> Manage Users
                    </a>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Manage Users</h1>
                    <a href="add_user.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Add New User
                    </a>
                </div>
                
                <?php if(isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if(isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <a href="manage_users.php?role=all<?php echo !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-outline-primary <?php echo empty($role_filter) ? 'active' : ''; ?>">All</a>
                                    <a href="manage_users.php?role=admin<?php echo !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-outline-primary <?php echo $role_filter == 'admin' ? 'active' : ''; ?>">Admin</a>
                                    <a href="manage_users.php?role=staff<?php echo !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-outline-primary <?php echo $role_filter == 'staff' ? 'active' : ''; ?>">Staff</a>
                                    <a href="manage_users.php?role=customer<?php echo !empty($search) ? '&search='.$search : ''; ?>" class="btn btn-outline-primary <?php echo $role_filter == 'customer' ? 'active' : ''; ?>">Customer</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="manage_users.php" method="get" class="d-flex">
                                    <?php if(!empty($role_filter)): ?>
                                        <input type="hidden" name="role" value="<?php echo $role_filter; ?>">
                                    <?php endif; ?>
                                    <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="<?php echo $search; ?>">
                                    <button type="submit" class="btn btn-outline-primary">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result->num_rows > 0): ?>
                                        <?php while($user = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo $user['username']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($user['role']) {
                                                            case 'admin': echo 'danger'; break;
                                                            case 'staff': echo 'warning'; break;
                                                            case 'customer': echo 'success'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#roleModal<?php echo $user['id']; ?>">
                                                            <i class="bi bi-pencil"></i> Role
                                                        </button>
                                                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                                                        <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <!-- Role Update Modal -->
                                                    <div class="modal fade" id="roleModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="roleModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="roleModalLabel<?php echo $user['id']; ?>">Update User Role</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="manage_users.php" method="post">
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                        <div class="mb-3">
                                                                            <label for="role<?php echo $user['id']; ?>" class="form-label">Role</label>
                                                                            <select class="form-select" id="role<?php echo $user['id']; ?>" name="role" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                                                <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                                                                <option value="customer" <?php echo $user['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                                            </select>
                                                                            <?php if($user['id'] == $_SESSION['user_id']): ?>
                                                                                <div class="form-text text-danger">You cannot change your own role.</div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                        <button type="submit" name="update_role" class="btn btn-primary" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>Update Role</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo !empty($role_filter) ? '&role='.$role_filter : ''; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>">Previous</a>
                                    </li>
                                    
                                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($role_filter) ? '&role='.$role_filter : ''; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo !empty($role_filter) ? '&role='.$role_filter : ''; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
