<?php
session_start();
ini_set('display_errors', 0);
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Add this check for admin pages
if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false && !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit();
}
?>