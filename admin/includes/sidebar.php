<div class="sidebar p-3" style="width: 280px;">
    <h4 class="mb-4 text-white">Deshika Admin</h4>
    <hr style="border-color: var(--light-pink); opacity: 0.5;">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="../dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'products/') !== false ? 'active' : ''; ?>" href="../products/view.php">
                <i class="fas fa-tshirt me-2"></i> Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'categories/') !== false ? 'active' : ''; ?>" href="../categories/view.php">
                <i class="fas fa-list me-2"></i> Categories
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'orders/') !== false ? 'active' : ''; ?>" href="../orders/view.php">
                <i class="fas fa-shopping-bag me-2"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'users/') !== false ? 'active' : ''; ?>" href="../users/view.php">
                <i class="fas fa-users me-2"></i> Users
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link" href="../../index.php" style="border-top: 1px solid var(--light-pink); padding-top: 10px;">
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

<style>
    .sidebar {
        background-color: var(--dark-pink);
        color: var(--white);
    }
    
    .sidebar .nav-link {
        color: var(--white);
        margin: 5px 0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .sidebar .nav-link:hover {
        background-color: var(--primary-color);
        transform: translateX(5px);
    }
    
    .sidebar .nav-link.active {
        background-color: var(--primary-color);
        font-weight: bold;
        box-shadow: 3px 0 0 var(--white) inset;
    }
    
    .sidebar hr {
        border-color: var(--light-pink);
        opacity: 0.5;
    }
    
    .sidebar i {
        width: 20px;
        text-align: center;
    }
</style>