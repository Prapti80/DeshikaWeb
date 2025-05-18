<?php
session_start(); // Critical addition
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/Deshika/';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Check if user exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Check if email is verified
            if (!$user['is_verified']) {
                $_SESSION['login_error'] = "Please verify your email first";
                header("Location: login.php");
                exit();
            }
            
           // After successful login
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['profile_image'] = $user['profile_image']; // Add this line
            

            if ($user['is_admin']) {
               header("Location: " . $base_url . "admin/dashboard.php");
                exit();
            } else {
                header("Location: ../index.php");
                exit();
            }
        }
    }
    
    // If we get here, login failed
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>