<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get order details
$order_query = "SELECT o.*, u.name as customer_name, u.email, u.phone, u.address 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = '$order_id'";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    header("Location: view.php");
    exit();
}

// Get order items
// Get order items with product details
$items_query = "SELECT oi.*, p.name as product_name, p.image, p.price as product_price
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = '$order_id'";
$items_result = mysqli_query($conn, $items_query);
$items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update both order_status and payment_status if needed
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);
    $payment_status = isset($_POST['payment_status']) ? mysqli_real_escape_string($conn, $_POST['payment_status']) : null;
    
    if ($payment_status) {
        $update_query = "UPDATE orders SET 
                        order_status = '$order_status', 
                        payment_status = '$payment_status' 
                        WHERE id = '$order_id'";
    } else {
        $update_query = "UPDATE orders SET 
                        order_status = '$order_status' 
                        WHERE id = '$order_id'";
    }
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Order updated successfully!";
        header("Location: view.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Use your existing admin styles */
        :root {
            --primary-color: #ff6b8b;
            --secondary-color: #ff8e9e;
            --light-pink: #ffdfe5;
            --dark-pink: #e84393;
        }
        
        .status-pending { color: #ffc107; }
        .status-processing { color: #17a2b8; }
        .status-completed { color: #28a745; }
        .status-cancelled { color: #dc3545; }

        /* Add these to your styles */
        .status-processing { 
            color: #17a2b8;
            font-weight: 500;
        }
        .status-shipped { 
            color: #007bff;
            font-weight: 500;
        }
        .status-delivered { 
            color: #28a745;
            font-weight: 500;
        }
        .status-cancelled { 
            color: #dc3545;
            font-weight: 500;
            text-decoration: line-through;
        }

        .payment-pending { color: #ffc107; }
        .payment-paid { color: #28a745; }
        .payment-failed { color: #dc3545; }
    </style>
</head>
<body>
    <div class="wrapper d-flex">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Order Details #<?php echo $order['id']; ?></h2>
                <a href="view.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            
            <!-- Success/Error messages -->
            
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
                                                            <h6 class="mb-0"><?php echo $item['product_name']; ?></h6>
                                                            <small>Size: <?php echo $item['size']; ?></small>
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
                        <!-- Update the status form section: -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Order Status</label>
                                <select name="order_status" class="form-select" required>
                                    <option value="processing" <?= $order['order_status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="shipped" <?= $order['order_status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['order_status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select" required>
                                    <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= $order['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                                </select>
                            </div>
                            
                            <!-- Add hidden field to maintain order ID -->
                            <input type="hidden" name="id" value="<?php echo $order_id; ?>">
                            
                            <button type="submit" class="btn btn-pink w-100">
                                <i class="fas fa-save me-1"></i> Update Order
                            </button>
                        </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-pink text-white">
                            <h5 class="mb-0">Customer Details</h5>
                        </div>
                        <div class="card-body">
                            <h6><?php echo $order['customer_name']; ?></h6>
                            <p class="mb-1"><?php echo $order['email']; ?></p>
                            <p class="mb-1"><?php echo $order['phone']; ?></p>
                            <p class="mb-0"><?php echo nl2br($order['address']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>