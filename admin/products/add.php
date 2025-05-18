<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

// Get categories for dropdown
$categories_query = "SELECT * FROM categories";
$categories_result = mysqli_query($conn, $categories_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Deshika Admin</title>
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
    </style>
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
                <h2>Add New Product</h2>
                <a href="view.php" class="btn btn-pink">
                    <i class="fas fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form action="add_process.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_price" class="form-label">Discount Price (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sizes" class="form-label">Available Sizes (comma separated)</label>
                                    <input type="text" class="form-control" id="sizes" name="sizes" placeholder="S,M,L,XL" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="colors" class="form-label">Available Colors (comma separated)</label>
                                    <input type="text" class="form-control" id="colors" name="colors" placeholder="Red,Blue,Green" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock Quantity</label>
                                    <input type="number" class="form-control" id="stock" name="stock" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                                    <label class="form-check-label" for="is_featured">Featured Product</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-pink">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>