<?php
ob_start();
include 'includes/header.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get cart items
$cart_query = "SELECT c.*, p.name, p.price, p.discount_price 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = $user_id";
$cart_result = mysqli_query($conn, $cart_query);
$cart_items = mysqli_fetch_all($cart_result, MYSQLI_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
    $total += $price * $item['quantity'];
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create order
    $order_query = "INSERT INTO orders (user_id, total_amount) VALUES ($user_id, $total)";
    
    if (mysqli_query($conn, $order_query)) {
        $order_id = mysqli_insert_id($conn);
        
        // Add order items
        foreach ($cart_items as $item) {
            $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
            $insert_item = "INSERT INTO order_items (order_id, product_id, quantity, price, size, color) 
                           VALUES ($order_id, ".$item['product_id'].", ".$item['quantity'].", $price, '".$item['size']."', '".$item['color']."')";
            mysqli_query($conn, $insert_item);
        }
        
        // Clear cart
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
        
        // Redirect to thank you page
        $_SESSION['order_id'] = $order_id;
        header("Location: checkout_success.php");
        exit();
    } else {
        $error = "Error creating order: " . mysqli_error($conn);
    }
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-4" style="color: var(--dark-pink);">Delivery Address</h4>
                    
                    <form id="checkoutForm" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" required>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="saveAddress">
                            <label class="form-check-label" for="saveAddress">Save this address for future use</label>
                        </div>
                        
                        <h4 class="mb-4 mt-5" style="color: var(--dark-pink);">Payment Method</h4>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery (COD)
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment" id="card" value="card">
                            <label class="form-check-label" for="card">
                                Credit/Debit Card
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" id="upi" value="upi">
                            <label class="form-check-label" for="upi">
                                UPI Payment
                            </label>
                        </div>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <button type="submit" class="btn btn-pink mt-4 w-100">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4" style="color: var(--dark-pink);">Order Summary</h4>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                        <span>₹<?php echo $total; ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Charges</span>
                        <span>FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total Amount</span>
                        <span class="fw-bold">₹<?php echo $total; ?></span>
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3">Your Items</h5>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $item['name']; ?> x<?php echo $item['quantity']; ?></span>
                            <span>₹<?php echo ($item['discount_price'] > 0 ? $item['discount_price'] : $item['price']) * $item['quantity']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Save address to user profile
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    if (document.getElementById('saveAddress').checked) {
        const formData = new FormData(this);
        
        fetch('profile/save_address.php', {
            method: 'POST',
            body: formData
        });
    }
});
</script>

<?php
include 'includes/footer.php';
?>