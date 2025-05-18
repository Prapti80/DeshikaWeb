<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product data with category name
$product_query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = $product_id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    $_SESSION['error'] = "Product not found";
    header("Location: view.php");
    exit();
}

// Get categories for dropdown
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// Get all product images if you have multiple images per product
$images_query = "SELECT * FROM product_images WHERE product_id = $product_id ORDER BY is_primary DESC";
$images_result = mysqli_query($conn, $images_query);
$product_images = mysqli_fetch_all($images_result, MYSQLI_ASSOC);
// Initialize empty array for product images
$product_images = [];

// Check if product_images table exists and get images
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'product_images'");
if (mysqli_num_rows($table_check) > 0) {
    $images_query = "SELECT * FROM product_images WHERE product_id = $product_id ORDER BY is_primary DESC";
    $images_result = mysqli_query($conn, $images_query);
    if ($images_result) {
        $product_images = mysqli_fetch_all($images_result, MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Deshika Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6b8b;
            --secondary-color: #ff8e9e;
            --light-pink: #ffdfe5;
            --dark-pink: #e84393;
            --white: #ffffff;
            --black: #333333;
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--dark-pink);
            color: white;
        }
        
        .sidebar .nav-link {
            color: white;
            margin: 5px 0;
            border-radius: 5px;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--primary-color);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            font-weight: bold;
        }
        
        .main-content {
            background-color: #f8f9fa;
        }
        
        .btn-pink {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-pink:hover {
            background-color: var(--dark-pink);
            color: white;
        }
        
        .current-image {
            max-width: 200px;
            max-height: 200px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
        }
        
        .image-thumbnail {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 10px;
            position: relative;
        }
        
        .delete-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .color-preview {
            width: 25px;
            height: 25px;
            display: inline-block;
            border: 1px solid #ddd;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .tagify {
            width: 100%;
        }
        
        .tab-content {
            padding: 20px;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
    </style>
    <!-- Tagify for tags input -->
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 280px;">
            <h4 class="mb-4">Deshika Admin</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="view.php">
                        <i class="fas fa-tshirt me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../categories/view.php">
                        <i class="fas fa-list me-2"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../orders/view.php">
                        <i class="fas fa-shopping-bag me-2"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../users/view.php">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../../index.php">
                        <i class="fas fa-store me-2"></i> Visit Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../auth/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h2>
                <div>
                    <a href="../../product/<?php echo $product['slug']; ?>" target="_blank" class="btn btn-info me-2">
                        <i class="fas fa-eye me-1"></i> View Live
                    </a>
                    <a href="view.php" class="btn btn-pink">
                        <i class="fas fa-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form action="update.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
                        
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Basic Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing" type="button" role="tab">Pricing</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">Inventory</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab">Images</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab">SEO</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="productTabsContent">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Product Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Category *</label>
                                            <select class="form-select" id="category" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php mysqli_data_seek($categories_result, 0); ?>
                                                <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                                    <option value="<?php echo $category['id']; ?>" 
                                                        <?php if ($category['id'] == $product['category_id']) echo 'selected'; ?>>
                                                        <?php echo $category['name']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="short_description" class="form-label">Short Description</label>
                                            <textarea class="form-control" id="short_description" name="short_description" 
                                                      rows="3"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
                                            <small class="text-muted">Brief description for product listings (optional)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Full Description *</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Pricing Tab -->
                            <div class="tab-pane fade" id="pricing" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Regular Price (₹) *</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="price" 
                                                   name="price" value="<?php echo $product['price']; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="cost_price" class="form-label">Cost Price (₹)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="cost_price" 
                                                   name="cost_price" value="<?php echo $product['cost_price'] ?? ''; ?>">
                                            <small class="text-muted">For internal reference only</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="discount_price" class="form-label">Sale Price (₹)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="discount_price" 
                                                   name="discount_price" value="<?php echo $product['discount_price']; ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Sale Schedule</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="datetime-local" class="form-control" name="sale_start" 
                                                           value="<?php echo !empty($product['sale_start']) ? date('Y-m-d\TH:i', strtotime($product['sale_start'])) : ''; ?>">
                                                    <small class="text-muted">Start date</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="datetime-local" class="form-control" name="sale_end" 
                                                           value="<?php echo !empty($product['sale_end']) ? date('Y-m-d\TH:i', strtotime($product['sale_end'])) : ''; ?>">
                                                    <small class="text-muted">End date</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Inventory Tab -->
                            <div class="tab-pane fade" id="inventory" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
                                            <input type="text" class="form-control" id="sku" name="sku" 
                                                   value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stock Quantity *</label>
                                            <input type="number" min="0" class="form-control" id="stock" name="stock" 
                                                   value="<?php echo $product['stock']; ?>" required>
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="manage_stock" name="manage_stock" value="1" 
                                                   <?php echo ($product['manage_stock'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="manage_stock">Manage stock level</label>
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="backorders" name="backorders" value="1" 
                                                   <?php echo ($product['backorders'] ?? 0) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="backorders">Allow backorders</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sizes" class="form-label">Available Sizes *</label>
                                            <input type="text" class="form-control" id="sizes" name="sizes" 
                                                   value="<?php echo $product['sizes']; ?>" placeholder="S,M,L,XL" required>
                                            <small class="text-muted">Comma separated list</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="colors" class="form-label">Available Colors *</label>
                                            <input type="text" class="form-control" id="colors" name="colors" 
                                                   value="<?php echo $product['colors']; ?>" placeholder="Red,Blue,Green" required>
                                            <small class="text-muted">Comma separated list</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="weight" class="form-label">Weight (kg)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="weight" 
                                                   name="weight" value="<?php echo $product['weight'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Images Tab -->
                        <div class="tab-pane fade" id="images" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Main Product Image *</label>
                                        <div>
                                            <img src="../../assets/images/products/<?php echo $product['image']; ?>" 
                                                class="current-image" alt="Current Image">
                                        </div>
                                        <input type="file" class="form-control mt-2" id="image" name="image" accept="image/*">
                                        <small class="text-muted">Recommended size: 800x800px</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Additional Images</label>
                                        <?php if (mysqli_num_rows($table_check) > 0): ?>
                                            <input type="file" class="form-control" name="additional_images[]" multiple accept="image/*">
                                            <small class="text-muted">You can upload multiple images at once</small>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Current Additional Images</label>
                                                <div class="d-flex flex-wrap">
                                                    <?php if (!empty($product_images)): ?>
                                                        <?php foreach ($product_images as $image): ?>
                                                            <div class="position-relative">
                                                                <img src="../../assets/images/products/<?php echo $image['image_path']; ?>" 
                                                                    class="image-thumbnail rounded">
                                                                <button type="button" class="delete-image-btn" 
                                                                        data-image-id="<?php echo $image['id']; ?>">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <p class="text-muted">No additional images</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Multiple images feature is not setup yet. Please create the 'product_images' table in your database.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                            
                            <!-- SEO Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="slug" class="form-label">URL Slug *</label>
                                            <input type="text" class="form-control" id="slug" name="slug" 
                                                   value="<?php echo htmlspecialchars($product['slug']); ?>" required>
                                            <small class="text-muted">SEO-friendly URL identifier</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                                   value="<?php echo htmlspecialchars($product['meta_title'] ?? ''); ?>">
                                            <small class="text-muted">Title for search engines (50-60 chars)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" 
                                                      rows="3"><?php echo htmlspecialchars($product['meta_description'] ?? ''); ?></textarea>
                                            <small class="text-muted">Description for search engines (150-160 chars)</small>
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" 
                                                   <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_featured">Featured Product</label>
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                                   <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">Active (visible in store)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i> Delete Product
                            </button>
                            <div>
                                <button type="submit" name="save" value="save" class="btn btn-secondary me-2">
                                    <i class="fas fa-save me-1"></i> Save
                                </button>
                                <button type="submit" name="save" value="save_close" class="btn btn-pink">
                                    <i class="fas fa-save me-1"></i> Save & Close
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Deleting this product will also remove all associated images and inventory data.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="delete.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tagify for tags input -->
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <script>
        // Auto-generate slug from product name
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '') // Remove invalid chars
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(/-+/g, '-'); // Replace multiple - with single -
            
            document.getElementById('slug').value = slug;
        });

        // Initialize Tagify for sizes and colors
        new Tagify(document.getElementById('sizes'), {
            delimiters: ",| ",
            pattern: /^[a-zA-Z0-9\s]+$/,
            dropdown: {
                enabled: 1,
                maxItems: 10
            }
        });

        new Tagify(document.getElementById('colors'), {
            delimiters: ",| ",
            pattern: /^[a-zA-Z\s]+$/,
            dropdown: {
                enabled: 1,
                maxItems: 10
            }
        });

        // Handle additional image deletion
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imageId = this.getAttribute('data-image-id');
                if (confirm('Are you sure you want to delete this image?')) {
                    fetch('delete_image.php?id=' + imageId, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.parentElement.remove();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
                }
            });
        });

        // Show character count for meta fields
        document.getElementById('meta_title').addEventListener('input', function() {
            const count = this.value.length;
            const feedback = count > 60 ? 'text-danger' : 'text-success';
            this.nextElementSibling.innerHTML = `Title for search engines (${count}/60 chars)`;
            this.nextElementSibling.className = `text-muted ${feedback}`;
        });

        document.getElementById('meta_description').addEventListener('input', function() {
            const count = this.value.length;
            const feedback = count > 160 ? 'text-danger' : 'text-success';
            this.nextElementSibling.innerHTML = `Description for search engines (${count}/160 chars)`;
            this.nextElementSibling.className = `text-muted ${feedback}`;
        });
    </script>
</body>
</html>