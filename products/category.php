<?php
include '../includes/header.php';
require_once '../config/database.php';

// Get category from URL
$category_slug = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

// Get category details
$category_query = "SELECT * FROM categories WHERE slug='$category_slug'";
$category_result = mysqli_query($conn, $category_query);
$category = mysqli_fetch_assoc($category_result);

if (!$category) {
    header("Location: ../index.php");
    exit();
}

// Get products in this category
$products_query = "SELECT * FROM products WHERE category_id=".$category['id'];
$products_result = mysqli_query($conn, $products_query);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $category['name']; ?></li>
                </ol>
            </nav>
            <h1 class="mb-4" style="color: var(--dark-pink);"><?php echo $category['name']; ?></h1>
            <p><?php echo $category['description']; ?></p>
        </div>
    </div>
    
    <div class="row">
        <?php if (mysqli_num_rows($products_result) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                <?php
                $discount = '';
                if ($product['discount_price'] > 0) {
                    $discount = '<span class="badge badge-discount bg-danger">'.round(($product['price'] - $product['discount_price']) / $product['price'] * 100).'% OFF</span>';
                }
                ?>
                
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-card h-100">
                        <a href="details.php?id=<?php echo $product['id']; ?>">
                            <img src="../assets/images/products/<?php echo $product['image']; ?>" class="card-img-top product-img" alt="<?php echo $product['name']; ?>">
                            <div class="card-body">
                                <?php echo $discount; ?>
                                <h6 class="card-title"><?php echo $product['name']; ?></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if ($product['discount_price'] > 0): ?>
                                            <span class="text-danger fw-bold">₹<?php echo $product['discount_price']; ?></span>
                                            <span class="text-muted text-decoration-line-through ms-2">₹<?php echo $product['price']; ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold">₹<?php echo $product['price']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-sm btn-pink"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4>No products found in this category.</h4>
                <a href="../index.php" class="btn btn-pink mt-3">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include '../includes/footer.php';
?>