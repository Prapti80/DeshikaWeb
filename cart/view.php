<?php
ob_start();

include '../includes/header.php';
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get cart items for the current user
$user_id = $_SESSION['user_id'];
$cart_query = "SELECT c.*, p.name, p.price, p.discount_price, p.image 
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
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4" style="color: var(--dark-pink);">Your Shopping Cart</h1>
            
            <?php if (count($cart_items) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                $price = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                                $subtotal = $price * $item['quantity'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../assets/images/products/<?php echo $item['image']; ?>" width="80" class="me-3">
                                            <div>
                                                <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                                                <?php if ($item['discount_price'] > 0): ?>
                                                    <small class="text-muted text-decoration-line-through">₹<?php echo $item['price']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹<?php echo $price; ?></td>
                                    <td><?php echo $item['size'] ? $item['size'] : '-'; ?></td>
                                    <td>
                                        <?php if ($item['color']): ?>
                                            <span class="d-inline-block rounded-circle" style="width: 20px; height: 20px; background-color: <?php echo $item['color']; ?>"></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="input-group" style="width: 120px;">
                                            <button class="btn btn-outline-dark update-qty" data-id="<?php echo $item['id']; ?>" data-action="decrease">-</button>
                                            <input type="text" class="form-control text-center qty-input" value="<?php echo $item['quantity']; ?>" data-id="<?php echo $item['id']; ?>">
                                            <button class="btn btn-outline-dark update-qty" data-id="<?php echo $item['id']; ?>" data-action="increase">+</button>
                                        </div>
                                    </td>
                                    <td>₹<?php echo $subtotal; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-item" data-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Have a coupon?</h5>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Coupon code">
                                    <button class="btn btn-pink">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Cart Summary</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>₹<?php echo $total; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>Free</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Total:</span>
                                    <span class="fw-bold">₹<?php echo $total; ?></span>
                                </div>
                                <a href="../checkout.php" class="btn btn-pink w-100">Proceed to Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <h4 class="mb-3">Your cart is empty</h4>
                    <p class="mb-4">Looks like you haven't added anything to your cart yet</p>
                    <a href="../index.php" class="btn btn-pink">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Update quantity
document.querySelectorAll('.update-qty').forEach(btn => {
    btn.addEventListener('click', function() {
        const cartId = this.getAttribute('data-id');
        const action = this.getAttribute('data-action');
        const qtyInput = document.querySelector(`.qty-input[data-id="${cartId}"]`);
        let newQty = parseInt(qtyInput.value);
        
        if (action === 'increase') {
            newQty += 1;
        } else if (action === 'decrease' && newQty > 1) {
            newQty -= 1;
        }
        
        // Update via AJAX
        fetch('update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${cartId}&quantity=${newQty}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                qtyInput.value = newQty;
                location.reload(); // Refresh to update totals
            } else {
                alert('Error: ' + data.message);
            }
        });
    });
});

// Remove item
document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove this item?')) {
            const cartId = this.getAttribute('data-id');
            
            fetch('remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${cartId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to update cart
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    });
});
</script>

<?php
include '../includes/footer.php';
?>