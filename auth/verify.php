<?php
require_once '../config/database.php';
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load PHPMailer
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    error_log("Verification attempt - Email: $email, Token: $token");

    // Check if token exists and matches
    $query = "SELECT * FROM users WHERE email='$email' AND verification_token='$token' AND is_verified=0";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        error_log("Token found for user: " . $user['name']);
        
        // Check if token is expired
        if (strtotime($user['token_expiry']) < time()) {
            error_log("Token expired for user: " . $user['email']);
            
            // Generate new token and expiry
            $new_token = bin2hex(random_bytes(32));
            $new_expiry = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
            
            $update = "UPDATE users SET verification_token='$new_token', token_expiry='$new_expiry' WHERE email='$email'";
            
            if (mysqli_query($conn, $update)) {
                // Resend verification email
                $mail = new PHPMailer(true);
                
                try {
                    // Server settings
                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'prapticb9@gmail.com';
                    $mail->Password   = 'menx luxd dauf bbsz';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;
                    
                    // Recipients
                    $mail->setFrom('noreply@deshika.com', 'Deshika Fashion');
                    $mail->addAddress($email, $user['name']);
                    
                    // Get correct base URL
                    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                    $script_path = dirname($_SERVER['SCRIPT_NAME']);
                    $new_verification_link = "$base_url$script_path/verify.php?email=".urlencode($email)."&token=$new_token";
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'New Verification Link - Deshika Fashion';
                    $mail->Body    = "
                        <h2>Hello {$user['name']}!</h2>
                        <p>Your previous verification link has expired. Here's a new one:</p>
                        <a href='$new_verification_link' style='background:#ff6b8b; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>
                            Verify Email
                        </a>
                        <p>Or copy this link to your browser:<br>$new_verification_link</p>
                        <p>This link will expire in 24 hours.</p>
                    ";
                    $mail->AltBody = "Verify your Deshika account by visiting: $new_verification_link";
                    
                    $mail->send();
                    error_log("New verification email sent to: " . $email);
                    
                    $_SESSION['verification_error'] = "Verification link expired. A new link has been sent to your email.";
                    header("Location: register.php");
                    exit();
                    
                } catch (Exception $e) {
                    error_log("Email resend failed: " . $e->getMessage());
                    $_SESSION['verification_error'] = "Failed to send new verification email. Please try registering again.";
                    header("Location: register.php");
                    exit();
                }
            }
        }
        
        // Mark as verified
        $update = "UPDATE users SET is_verified=1, verification_token=NULL, token_expiry=NULL WHERE email='$email'";
        if (mysqli_query($conn, $update)) {
            error_log("User verified successfully: " . $email);
            
            // Set a success message that will be displayed on login page
            $_SESSION['verification_success'] = [
                'status' => true,
                'message' => 'Email verified successfully! You can now login.',
                'email' => $email
            ];
            
            header("Location: login.php?verified=1");
            exit();
        } else {
            error_log("Database update failed for: " . $email);
            $_SESSION['verification_error'] = "Database error during verification. Please try again.";
            header("Location: register.php");
            exit();
        }
    } else {
        error_log("Invalid token or already verified - Email: $email, Token: $token");
        $_SESSION['verification_error'] = "Invalid or expired verification link. Please try registering again.";
        header("Location: register.php");
        exit();
    }
}

$_SESSION['verification_error'] = "Invalid verification request. Please use the link from your email.";
header("Location: register.php");
exit();
?>