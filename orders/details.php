<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify order belongs to user
$order_query = "SELECT o.*, u.name, u.email, u.phone, u.address 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: view.php");
    exit();
}

// Get order items
$items_query = "SELECT oi.*, p.name, p.image, p.price 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
$items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <?php include '../../includes/header.php'; ?>
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Order #<?php echo $order['id']; ?></h1>
            <a href="view.php" class="btn btn-outline-secondary">Back to Orders</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-pink text-white">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../../assets/images/products/<?php echo $item['image']; ?>" 
                                                         class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                                                        <small>Size: <?php echo $item['size'] ?? 'N/A'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-pink text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Order Status</label>
                            <p class="form-control-static">
                                <span class="badge bg-<?php 
                                    echo match($order['order_status']) {
                                        'processing' => 'primary',
                                        'shipped' => 'warning',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>
                            <p class="form-control-static">
                                <span class="badge bg-<?php 
                                    echo match($order['payment_status']) {
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Order Date</label>
                            <p class="form-control-static"><?php echo date('d M Y h:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Total Amount</label>
                            <p class="form-control-static">₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-pink text-white">
                        <h5 class="mb-0">Shipping Details</h5>
                    </div>
                    <div class="card-body">
                        <h6><?php echo $order['name']; ?></h6>
                        <p class="mb-1"><?php echo $order['email']; ?></p>
                        <p class="mb-1"><?php echo $order['phone'] ?? 'Not provided'; ?></p>
                        <p class="mb-0"><?php echo nl2br($order['address'] ?? 'No address provided'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>