<?php
require_once '../config/database.php';
session_start();

// Enhanced error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load PHPMailer
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

        // Validation
        $errors = [];
        
        if (strlen($name) < 2 || strlen($name) > 50) {
            throw new Exception("Name must be between 2-50 characters");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (!preg_match('/^[0-9]{10,15}$/', preg_replace('/\D/', '', $phone))) {
            throw new Exception("Invalid phone number");
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            throw new Exception("Password must contain:<br>
                     - At least 8 characters<br>
                     - At least one uppercase letter<br>
                     - At least one lowercase letter<br>
                     - At least one number<br>
                     - At least one special character (@$!%*?&)");
        }
        
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }

        // Check if email exists
        $check_email = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (!$result) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($user['is_verified']) {
                throw new Exception("Email already registered");
            } else {
                throw new Exception("Email already registered but not verified. Check your email for verification link.");
            }
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate verification token and expiry
        $verification_token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
        
        // Insert user with verification data
        $query = "INSERT INTO users (name, email, phone, address, password, verification_token, token_expiry, is_verified) 
                  VALUES ('$name', '$email', '$phone', '$address', '$hashed_password', '$verification_token', '$token_expiry', 0)";
        
        if (!mysqli_query($conn, $query)) {
            throw new Exception("Registration failed: " . mysqli_error($conn));
        }

        $verification_link = "http://".$_SERVER['HTTP_HOST']."/deshika/auth/verify.php?email=".urlencode($email)."&token=$verification_token";
        
        // Configure PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'prapticb9@gmail.com';
            $mail->Password   = 'menx luxd dauf bbsz';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            
            // Recipients
            $mail->setFrom('noreply@deshika.com', 'Deshika Fashion');
            $mail->addAddress($email, $name);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Deshika Account';
            $mail->Body    = "
                <h2>Welcome to Deshika Fashion, $name!</h2>
                <p>Please click below to verify your email address:</p>
                <a href='$verification_link' style='background:#ff6b8b; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                    Verify Email
                </a>
                
                <p>This link will expire in 24 hours.</p>
            ";
            $mail->AltBody = "Verify your Deshika account by visiting: $verification_link";
            
            $mail->send();
            
            // Redirect to success page
            $_SESSION['registration_success'] = true;
            $_SESSION['email'] = $email;
            header("Location: login.php?registration=success");
            exit();
            
        } catch (Exception $e) {
            error_log("Email send failed: " . $e->getMessage());
            throw new Exception("We couldn't send the verification email. Please try again later.");
        }

    } catch (Exception $e) {
        $_SESSION['register_error'] = $e->getMessage();
        $_SESSION['register_data'] = $_POST;
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>