<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Set default image path
$defaultImage = '/Deshika/assets/images/profile/default.jpeg';

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Set profile image path
    if(isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])) {
        $profileImage = '/Deshika/assets/images/profile/' . $_SESSION['profile_image'];
        
        // Verify the image exists
        if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $profileImage)) {
            $profileImage = $defaultImage;
        }
    } else {
        $profileImage = $defaultImage;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deshika - Women's Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #ff6b8b;
            --secondary-color: #ff8e9e;
            --light-pink: #ffdfe5;
            --dark-pink: #e84393;
            --white: #ffffff;
            --black: #333333;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-pink);
        }
        
        .navbar {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--dark-pink) !important;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            height: 50px;
            margin-right: 10px;
        }
        
        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            margin: 0 10px;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .btn-pink {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-pink:hover {
            background-color: var(--dark-pink);
            color: white;
            transform: translateY(-2px);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 8px 20px;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--light-pink);
            color: var(--dark-pink);
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
            border: 2px solid var(--light-pink);
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: var(--dark-pink);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        /* 2025 Modern Footer Styles */
.footer {
    background: linear-gradient(135deg, #e84393 0%, #ff6b8b 100%);
    color: white;
    padding: 60px 0 30px;
    position: relative;
    overflow: hidden;
}

.footer:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(90deg, #ff4d6d, #ffb3c1, #ff4d6d);
}

.footer h5 {
    font-weight: 600;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
    color: white;
}

.footer h5:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 50px;
    height: 2px;
    background: white;
}

.footer a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    margin-bottom: 8px;
}

.footer a:hover {
    color: white;
    transform: translateX(5px);
}

.social-icons a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    margin-right: 12px;
    transition: all 0.3s ease;
}

.social-icons a:hover {
    background: white;
    color: var(--dark-pink) !important;
    transform: translateY(-3px);
}

.newsletter-input {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: white;
    border-radius: 50px 0 0 50px !important;
}

.newsletter-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.newsletter-btn {
    border-radius: 0 50px 50px 0 !important;
    background: white;
    color: var(--dark-pink) !important;
    border: none;
}
.user-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
    border: 2px solid var(--light-pink);
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
    margin-top: 40px;
}

/* Floating elements */
.floating-element {
    position: absolute;
    opacity: 0.1;
    z-index: 0;
}

.floating-1 {
    top: 10%;
    left: 5%;
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
    animation: float 8s ease-in-out infinite;
}

.floating-2 {
    bottom: 20%;
    right: 10%;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
    animation: float 10s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
    100% { transform: translateY(0) rotate(0deg); }
}

/* Payment methods */
.payment-methods img {
    filter: brightness(0) invert(1);
    opacity: 0.8;
    transition: all 0.3s ease;
}

.payment-methods img:hover {
    opacity: 1;
    transform: scale(1.1);
}
    </style>
</head>
<body>
   <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/Deshika/index.php">
            <img src="/Deshika/assets/images/logo.png" alt="Deshika Logo">
            Deshika
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/Deshika/index.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <li><a class="dropdown-item" href="/Deshika/products/category.php?category=saree"><i class="fas fa-vest me-2"></i> Saree</a></li>
                        <li><a class="dropdown-item" href="/Deshika/products/category.php?category=kurti"><i class="fas fa-tshirt me-2"></i> Kurti</a></li>
                        <li><a class="dropdown-item" href="/Deshika/products/category.php?category=tops"><i class="fas fa-shirt me-2"></i> Tops</a></li>
                        <li><a class="dropdown-item" href="/Deshika/products/category.php?category=lehenga"><i class="fas fa-ring me-2"></i> Lehenga</a></li>
                        <li><a class="dropdown-item" href="/Deshika/products/category.php?category=salwar-suit"><i class="fas fa-user-tie me-2"></i> Salwar Suit</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Deshika/contact.php">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item position-relative">
                    <a class="nav-link" href="/Deshika/cart/view.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span>
                    </a>
                </li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2" style="font-size: 1.25rem;"></i>
                            <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/Deshika/profile/view.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <li><a class="dropdown-item" href="/Deshika/admin/dashboard.php"><i class="fas fa-cog me-2"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/Deshika/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/Deshika/auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-pink ms-2" href="/Deshika/auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>