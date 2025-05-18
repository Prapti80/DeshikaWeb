<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Deshika</title>
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
        
        .card-counter {
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }
        
        .card-counter:hover {
            box-shadow: 4px 4px 20px rgba(0, 0, 0, 0.1);
            transition: .3s linear all;
        }
        
        .card-counter.primary {
            background-color: var(--primary-color);
            color: #FFF;
        }
        
        .card-counter.danger {
            background-color: #ef5350;
            color: #FFF;
        }  
        
        .card-counter.success {
            background-color: #66bb6a;
            color: #FFF;
        }  
        
        .card-counter.info {
            background-color: #26c6da;
            color: #FFF;
        }  
        
        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }
        
        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }
        
        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.8;
            display: block;
            font-size: 18px;
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
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products/view.php">
                        <i class="fas fa-tshirt me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories/view.php">
                        <i class="fas fa-list me-2"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="orders/view.php">
                        <i class="fas fa-shopping-bag me-2"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users/view.php">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-store me-2"></i> Visit Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1 p-4">
            <h2 class="mb-4">Dashboard Overview</h2>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card-counter primary">
                        <?php
                        $query = "SELECT COUNT(*) as total FROM products";
                        $result = mysqli_query($conn, $query);
                        $data = mysqli_fetch_assoc($result);
                        ?>
                        <i class="fas fa-tshirt"></i>
                        <span class="count-numbers"><?php echo $data['total']; ?></span>
                        <span class="count-name">Products</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-counter success">
                        <?php
                        $query = "SELECT COUNT(*) as total FROM orders";
                        $result = mysqli_query($conn, $query);
                        $data = mysqli_fetch_assoc($result);
                        ?>
                        <i class="fas fa-shopping-bag"></i>
                        <span class="count-numbers"><?php echo $data['total']; ?></span>
                        <span class="count-name">Orders</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-counter info">
                        <?php
                        $query = "SELECT COUNT(*) as total FROM users WHERE is_admin=0";
                        $result = mysqli_query($conn, $query);
                        $data = mysqli_fetch_assoc($result);
                        ?>
                        <i class="fas fa-users"></i>
                        <span class="count-numbers"><?php echo $data['total']; ?></span>
                        <span class="count-name">Customers</span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card-counter danger">
                        <?php
                        $query = "SELECT SUM(total_amount) as total FROM orders WHERE payment_status='paid'";
                        $result = mysqli_query($conn, $query);
                        $data = mysqli_fetch_assoc($result);
                        ?>
                        <i class="fas fa-rupee-sign"></i>
                        <span class="count-numbers">₹<?php echo number_format($data['total'] ?? 0, 2); ?></span>
                        <span class="count-name">Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT o.id, u.name, o.created_at, o.total_amount, o.order_status 
                                          FROM orders o 
                                          JOIN users u ON o.user_id = u.id 
                                          ORDER BY o.created_at DESC LIMIT 5";
                                $result = mysqli_query($conn, $query);
                                
                                while ($order = mysqli_fetch_assoc($result)) {
                                    $status_class = '';
                                    if ($order['order_status'] == 'processing') $status_class = 'bg-primary';
                                    if ($order['order_status'] == 'shipped') $status_class = 'bg-warning';
                                    if ($order['order_status'] == 'delivered') $status_class = 'bg-success';
                                    if ($order['order_status'] == 'cancelled') $status_class = 'bg-danger';
                                    
                                    echo '
                                    <tr>
                                        <td>#'.$order['id'].'</td>
                                        <td>'.$order['name'].'</td>
                                        <td>'.date('d M Y', strtotime($order['created_at'])).'</td>
                                        <td>₹'.$order['total_amount'].'</td>
                                        <td><span class="badge '.$status_class.'">'.ucfirst($order['order_status']).'</span></td>
                                        <td>
                                            <a href="orders/view.php?id='.$order['id'].'" class="btn btn-sm btn-pink">View</a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT p.id, p.name, p.price, p.stock, p.image, c.name as category_name 
                                          FROM products p 
                                          JOIN categories c ON p.category_id = c.id 
                                          ORDER BY p.created_at DESC LIMIT 5";
                                $result = mysqli_query($conn, $query);
                                
                                while ($product = mysqli_fetch_assoc($result)) {
                                    echo '
                                    <tr>
                                        <td>'.$product['id'].'</td>
                                        <td><img src="../assets/images/products/'.$product['image'].'" width="50"></td>
                                        <td>'.$product['name'].'</td>
                                        <td>'.$product['category_name'].'</td>
                                        <td>₹'.$product['price'].'</td>
                                        <td>'.$product['stock'].'</td>
                                        <td>
                                            <a href="products/edit.php?id='.$product['id'].'" class="btn btn-sm btn-pink">Edit</a>
                                        </td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- After the counters in dashboard.php -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Monthly Sales</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Product Categories</h5>
            </div>
            <div class="card-body">
                <canvas id="categoriesChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Sales (₹)',
                data: [12000, 19000, 15000, 18000, 22000, 25000],
                backgroundColor: 'rgba(255, 107, 139, 0.2)',
                borderColor: 'rgba(255, 107, 139, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesChart = new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: ['T-Shirts', 'Jeans', 'Dresses', 'Accessories'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: [
                    'rgba(255, 107, 139, 0.7)',
                    'rgba(232, 67, 147, 0.7)',
                    'rgba(255, 142, 158, 0.7)',
                    'rgba(255, 223, 229, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>