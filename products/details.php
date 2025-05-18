<?php
include '../includes/header.php';
require_once '../config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product details
$product_query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = $product_id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    header("Location: ../index.php");
    exit();
}

// Check if product is in wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wishlist_query = "SELECT id FROM wishlist WHERE user_id = ".$_SESSION['user_id']." AND product_id = $product_id";
    $wishlist_result = mysqli_query($conn, $wishlist_query);
    $in_wishlist = mysqli_num_rows($wishlist_result) > 0;
}

// Get related products (same category)
$related_query = "SELECT * FROM products WHERE category_id = ".$product['category_id']." AND id != $product_id LIMIT 4";
$related_result = mysqli_query($conn, $related_query);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="category.php?category=<?php echo strtolower(str_replace(' ', '-', $product['category_name'])); ?>"><?php echo $product['category_name']; ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card border-0">
                <img src="../assets/images/products/<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo $product['name']; ?>">
            </div>
        </div>
        <div class="col-md-6">
            <h1 class="mb-3" style="color: var(--dark-pink);"><?php echo $product['name']; ?></h1>
            <div class="d-flex align-items-center mb-3">
                <?php if ($product['discount_price'] > 0): ?>
                    <h3 class="text-danger me-3">₹<?php echo $product['discount_price']; ?></h3>
                    <h5 class="text-muted text-decoration-line-through">₹<?php echo $product['price']; ?></h5>
                    <span class="badge bg-danger ms-3"><?php echo round(($product['price'] - $product['discount_price']) / $product['price'] * 100); ?>% OFF</span>
                <?php else: ?>
                    <h3>₹<?php echo $product['price']; ?></h3>
                <?php endif; ?>
            </div>
            
            <p class="mb-4"><?php echo $product['description']; ?></p>
            
            <div class="mb-4">
                <h5 class="mb-2">Available Sizes:</h5>
                <div class="d-flex flex-wrap">
                    <?php 
                    $sizes = explode(',', $product['sizes']);
                    foreach ($sizes as $size): 
                    ?>
                        <button class="btn btn-outline-dark rounded-pill me-2 mb-2 size-btn"><?php echo trim($size); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <h5 class="mb-2">Available Colors:</h5>
                <div class="d-flex flex-wrap">
                    <?php 
                    $colors = explode(',', $product['colors']);
                    foreach ($colors as $color): 
                    ?>
                        <button class="btn rounded-pill me-2 mb-2 color-btn" style="background-color: <?php echo trim($color); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="input-group mb-3" style="max-width: 150px;">
                    <button class="btn btn-outline-dark" type="button" id="decrement">-</button>
                    <input type="text" class="form-control text-center" value="1" id="quantity">
                    <button class="btn btn-outline-dark" type="button" id="increment">+</button>
                </div>
            </div>
            
            <div class="d-flex flex-wrap">
                <button class="btn btn-pink me-3 mb-3" id="addToCart">
                    <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                </button>
                <button class="btn btn-outline-dark mb-3 <?php echo $in_wishlist ? 'active' : ''; ?>" id="addToWishlist">
                    <i class="fas fa-heart me-2"></i> <?php echo $in_wishlist ? 'In Wishlist' : 'Add to Wishlist'; ?>
                </button>
            </div>
            
            <div class="mt-4">
                <p class="mb-1"><i class="fas fa-truck me-2"></i> Free delivery on orders over ₹1000</p>
                <p class="mb-1"><i class="fas fa-undo me-2"></i> Easy 15 days returns</p>
                <p class="mb-0"><i class="fas fa-shield-alt me-2"></i> 100% Authentic Products</p>
            </div>
        </div>
    </div>
    
    <?php if (mysqli_num_rows($related_result) > 0): ?>
        <hr class="my-5">
        <h3 class="mb-4" style="color: var(--dark-pink);">You May Also Like</h3>
        <div class="row">
            <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                <?php
                $discount = '';
                if ($related['discount_price'] > 0) {
                    $discount = '<span class="badge badge-discount bg-danger">'.round(($related['price'] - $related['discount_price']) / $related['price'] * 100).'% OFF</span>';
                }
                ?>
                
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-card h-100">
                        <a href="details.php?id=<?php echo $related['id']; ?>">
                            <img src="../assets/images/products/<?php echo $related['image']; ?>" class="card-img-top product-img" alt="<?php echo $related['name']; ?>">
                            <div class="card-body">
                                <?php echo $discount; ?>
                                <h6 class="card-title"><?php echo $related['name']; ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($related['discount_price'] > 0): ?>
                                            <span class="text-danger fw-bold">₹<?php echo $related['discount_price']; ?></span>
                                            <span class="text-muted text-decoration-line-through ms-2">₹<?php echo $related['price']; ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold">₹<?php echo $related['price']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-pink"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .btn-outline-dark.active {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-dark.active i {
        color: #dc3545;
    }
    .btn-outline-dark:hover {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-dark:hover i {
        color: #dc3545;
    }
</style>

<script>
// Quantity increment/decrement
document.getElementById('increment').addEventListener('click', function() {
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
});

document.getElementById('decrement').addEventListener('click', function() {
    const quantityInput = document.getElementById('quantity');
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
    }
});

// Add to cart functionality
document.getElementById('addToCart').addEventListener('click', function() {
    const productId = <?php echo $product['id']; ?>;
    const quantity = document.getElementById('quantity').value;
    const selectedSize = document.querySelector('.size-btn.active') ? document.querySelector('.size-btn.active').textContent : '';
    const selectedColor = document.querySelector('.color-btn.active') ? document.querySelector('.color-btn.active').style.backgroundColor : '';
    
    <?php if(isset($_SESSION['user_id'])): ?>
        // AJAX call to add to cart
        fetch('../cart/add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}&size=${selectedSize}&color=${selectedColor}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart successfully!');
            } else {
                alert('Error: ' + data.message);
            }
        });
    <?php else: ?>
        alert('Please login to add items to cart');
        window.location.href = '../auth/login.php';
    <?php endif; ?>
});

// Add to wishlist functionality
document.getElementById('addToWishlist').addEventListener('click', function() {
    const productId = <?php echo $product['id']; ?>;
    const wishlistBtn = this;
    
    <?php if(isset($_SESSION['user_id'])): ?>
        const action = wishlistBtn.classList.contains('active') ? 'remove' : 'add';
        
        fetch('../wishlist/' + action + '.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (action === 'add') {
                    wishlistBtn.classList.add('active');
                    wishlistBtn.innerHTML = '<i class="fas fa-heart me-2"></i> In Wishlist';
                    alert('Product added to wishlist!');
                } else {
                    wishlistBtn.classList.remove('active');
                    wishlistBtn.innerHTML = '<i class="fas fa-heart me-2"></i> Add to Wishlist';
                    alert('Product removed from wishlist!');
                }
            } else {
                alert(data.message);
            }
        });
    <?php else: ?>
        alert('Please login to manage wishlist');
        window.location.href = '../auth/login.php';
    <?php endif; ?>
});

// Size and color selection
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

document.querySelectorAll('.color-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php
include '../includes/footer.php';
?>