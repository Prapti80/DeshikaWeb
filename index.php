<?php
include 'includes/header.php';
?>
<style>

</style>

<!-- Hero Section -->
<section class="hero-section py-5" style="background-color: var(--light-pink);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4" style="color: var(--dark-pink);">Elegant Fashion for the Modern Woman</h1>
                <p class="lead mb-4">Discover our exquisite collection of sarees, kurtis, and more, crafted with love and tradition.</p>
                <a href="products/category.php?category=saree" class="btn btn-pink btn-lg px-4 me-2">Shop Now</a>
                <a href="products/category.php?category=kurti" class="btn btn-outline-dark btn-lg px-4">Explore</a>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/hero-image.png" alt="Deshika Fashion" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5" style="background-color: #ffe4ec;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: var(--dark-pink);">Shop By Category</h2>
        <div class="row justify-content-center">
            <?php
            require_once 'config/database.php';
            $query = "SELECT * FROM categories LIMIT 5";
            $result = mysqli_query($conn, $query);

            while ($category = mysqli_fetch_assoc($result)) {
                echo '
                <div class="col-6 col-md-4 col-lg-2 mb-4">
                    <a href="products/category.php?category=' . $category['slug'] . '" class="text-decoration-none">
                        <div class="category-card text-center p-4 rounded-4" style="
                            background: linear-gradient(135deg, #fff 0%, #ffdfe5 100%);
                            border: 1px solid rgba(232, 67, 147, 0.2);
                            transition: all 0.3s ease;
                            height: 100%;
                        ">
                            <div class="category-icon mb-3" style="
                                font-size: 2.5rem;
                                color: var(--dark-pink);
                                background: linear-gradient(135deg, #ff6b8b 0%, #e84393 100%);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                            ">
                                ' . getCategoryIcon($category['slug']) . '
                            </div>
                            <h3 class="category-title m-0" style="
                                font-weight: 600;
                                color: var(--dark-pink);
                                font-size: 1.1rem;
                                position: relative;
                                display: inline-block;
                            ">
                                ' . $category['name'] . '
                                <span style="
                                    position: absolute;
                                    bottom: -5px;
                                    left: 0;
                                    width: 100%;
                                    height: 2px;
                                    background: linear-gradient(90deg, #ff6b8b, #e84393);
                                    transform: scaleX(0);
                                    transform-origin: right;
                                    transition: transform 0.3s ease;
                                "></span>
                            </h3>
                        </div>
                    </a>
                </div>';
            }
            
            function getCategoryIcon($slug) {
                $icons = [
                    'saree' => '<i class="fas fa-vest"></i>',
                    'kurti' => '<i class="fas fa-tshirt"></i>',
                    'tops' => '<i class="fas fa-shirt"></i>',
                    'lehenga' => '<i class="fas fa-ring"></i>',
                    'salwar-suit' => '<i class="fas fa-user-tie"></i>'
                ];
                return $icons[$slug] ?? '<i class="fas fa-shopping-bag"></i>';
            }
            ?>
        </div>
    </div>
</section>


<!-- Featured Products -->
<section class="py-5" style="background-color: #fff0f5;">
    <div class="container">
        <h2 class="text-center mb-5" style="color: var(--dark-pink);">Featured Products</h2>
        <div class="row">
            <?php
            $query = "SELECT * FROM products WHERE is_featured=1 LIMIT 8";
            $result = mysqli_query($conn, $query);
            
            while ($product = mysqli_fetch_assoc($result)) {
                $discount = '';
                if ($product['discount_price'] > 0) {
                    $discount = '<span class="badge badge-discount bg-danger">'.round(($product['price'] - $product['discount_price']) / $product['price'] * 100).'% OFF</span>';
                }
                
                echo '
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="product-card h-100 bg-white shadow-sm rounded">
                        <a href="products/details.php?id='.$product['id'].'">
                            <img src="assets/images/products/'.$product['image'].'" class="card-img-top product-img" alt="'.$product['name'].'">
                            <div class="card-body">
                                '.$discount.'
                                <h6 class="card-title">'.$product['name'].'</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>';
                if ($product['discount_price'] > 0) {
                    echo '<span class="text-danger fw-bold">₹'.$product['discount_price'].'</span>
                          <span class="text-muted text-decoration-line-through ms-2">₹'.$product['price'].'</span>';
                } else {
                    echo '<span class="fw-bold">₹'.$product['price'].'</span>';
                }
                echo '</div>
                                    <button class="btn btn-sm btn-pink"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>';
            }
            ?>
        </div>
        <div class="text-center mt-4">
            <a href="products/category.php?category=all" class="btn btn-pink">View All Products</a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5" style="background-color: var(--light-pink);">
    <div class="container">
        <h2 class="text-center mb-5" style="color: var(--dark-pink);">What Our Customers Say</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-white shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: var(--primary-color);"></i>
                        </div>
                        <p class="card-text">"The quality of the sarees is exceptional. I've received so many compliments!"</p>
                        <div class="mt-3">
                            <h6 class="mt-2 mb-0">Priya Sharma</h6>
                            <small class="text-muted">Regular Customer</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-white shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: var(--primary-color);"></i>
                        </div>
                        <p class="card-text">"Fast delivery and excellent customer service. Will definitely shop again!"</p>
                        <div class="mt-3">
                            <h6 class="mt-2 mb-0">Ananya Patel</h6>
                            <small class="text-muted">First-time Buyer</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-white shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x" style="color: var(--primary-color);"></i>
                        </div>
                        <p class="card-text">"The kurtis are so comfortable and stylish. Perfect for everyday wear."</p>
                        <div class="mt-3">
                           <h6 class="mt-2 mb-0">Riya Gupta</h6>
                            <small class="text-muted">Loyal Customer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
