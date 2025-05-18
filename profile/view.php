<?php
// Start session and check authentication first (before any output)
session_start();

ini_set('display_errors', 0); // Keep errors on during development
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];

// Handle success messages from other pages
if (isset($_GET['success'])) {
    $success_message = match($_GET['success']) {
        'profile_updated' => 'Profile updated successfully!',
        'password_changed' => 'Password changed successfully!',
        default => 'Action completed successfully!'
    };
}

// Get user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get recent orders with more details
$orders_query = "SELECT o.id, o.created_at, o.total_amount, o.order_status, o.payment_status, 
                        COUNT(oi.id) as item_count 
                 FROM orders o
                 LEFT JOIN order_items oi ON o.id = oi.order_id
                 WHERE o.user_id = $user_id 
                 GROUP BY o.id
                 ORDER BY o.created_at DESC 
                 LIMIT 3";
$orders_result = mysqli_query($conn, $orders_query);

// Get wishlist count
$wishlist_query = "SELECT COUNT(*) as wishlist_count FROM wishlist WHERE user_id = $user_id";
$wishlist_result = mysqli_query($conn, $wishlist_query);
$wishlist_data = mysqli_fetch_assoc($wishlist_result);
$wishlist_count = $wishlist_data['wishlist_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <?php include '../includes/header.php'; ?>
</head>
<body>
    <div class="container py-5">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4">
                <!-- Profile Card -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="../assets/images/profile/<?php echo $user['profile_image'] ?? 'default.png'; ?>" 
                             alt="avatar" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        <h5 class="my-3"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted mb-1">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                        <div class="d-flex justify-content-center mt-3">
                            <a href="edit.php" class="btn btn-pink btn-sm mx-1">Edit Profile</a>
                            <a href="change_password.php" class="btn btn-outline-pink btn-sm mx-1">Change Password</a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Quick Links</h5>
                        <div class="list-group">
                            <a href="view.php" class="list-group-item list-group-item-action active">
                                <i class="fas fa-user me-2"></i> Profile Overview
                            </a>
                            <a href="edit.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-edit me-2"></i> Edit Profile
                            </a>
                            <a href="change_password.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-lock me-2"></i> Change Password
                            </a>
                            <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Personal Information</h5>
                            <a href="edit.php" class="btn btn-sm btn-pink">Edit Profile</a>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Full Name</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['name']); ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <p class="form-control-static"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <p class="form-control-static"><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <p class="form-control-static">
                                    <?php if ($user['address']): ?>
                                        <?php echo nl2br(htmlspecialchars($user['address'])); ?>
                                    <?php else: ?>
                                        No address provided. <a href="edit.php">Add your address</a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Recent Orders</h5>
                            <a href="../orders/view.php" class="btn btn-sm btn-pink">View All</a>
                        </div>
                        
                        <?php if (mysqli_num_rows($orders_result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                            <?php
                                            $status_class = match($order['order_status']) {
                                                'processing' => 'bg-primary',
                                                'shipped' => 'bg-warning',
                                                'delivered' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo $order['item_count']; ?></td>
                                                <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $status_class; ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Payment: <?php echo ucfirst($order['payment_status']); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="../orders/details.php?id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-sm btn-outline-pink">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5>No orders yet</h5>
                                <p class="text-muted">You haven't placed any orders with us yet.</p>
                                <a href="../index.php" class="btn btn-pink">Start Shopping</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Account Security -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Account Security</h5>
                        <div class="alert alert-warning">
                            <i class="fas fa-shield-alt me-2"></i>
                            <strong>Keep your account secure</strong>
                            <p class="mb-0">Regularly update your password and never share it with anyone.</p>
                        </div>
                        <a href="change_password.php" class="btn btn-pink">
                            <i class="fas fa-lock me-2"></i> Change Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>