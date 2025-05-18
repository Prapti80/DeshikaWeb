<?php
include 'includes/header.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Check if order_id is set in session
if (!isset($_SESSION['order_id'])) {
    header("Location: cart.php");
    exit();
}

$order_id = $_SESSION['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$order_query = "SELECT o.*, u.name, u.email, u.phone 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

// Get order items
$items_query = "SELECT oi.*, p.name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);
$order_items = mysqli_fetch_all($items_result, MYSQLI_ASSOC);

// Clear the order_id from session so refreshing doesn't cause issues
unset($_SESSION['order_id']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" class="bi bi-check-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                    </div>
                    
                    <h2 class="mb-3" style="color: var(--dark-pink);">Order Confirmed!</h2>
                    <p class="lead mb-4">Thank you for your purchase, <?php echo $order['name']; ?>!</p>
                    
                    <div class="order-summary bg-light p-4 mb-4 rounded">
                        <h5 class="mb-3">Order Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order Number:</strong> #<?php echo str_pad($order_id, 8, '0', STR_PAD_LEFT); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong> ₹<?php echo $order['total_amount']; ?></p>
                                <p><strong>Payment Method:</strong> Cash on Delivery</p>
                            </div>
                        </div>
                    </div>
                    
                    <h5 class="mb-3">Your Items</h5>
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="images/products/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" width="50" class="me-3">
                                            <?php echo $item['name']; ?>
                                            <?php if ($item['size']): ?>
                                                <span class="badge bg-secondary ms-2">Size: <?php echo $item['size']; ?></span>
                                            <?php endif; ?>
                                            <?php if ($item['color']): ?>
                                                <span class="badge bg-secondary ms-2">Color: <?php echo $item['color']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="delivery-info bg-light p-4 mb-4 rounded">
                        <h5 class="mb-3">Delivery Information</h5>
                        <p>Your order will be delivered within 3-5 business days.</p>
                        <p>We'll send you a confirmation email to <strong><?php echo $order['email']; ?></strong> with tracking information once your order ships.</p>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="index.php" class="btn btn-outline-pink">Continue Shopping</a>
                        <a href="order_details.php?id=<?php echo $order_id; ?>" class="btn btn-pink">View Order Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>