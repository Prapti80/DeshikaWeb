<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Check if category is used in products
    $check_query = "SELECT COUNT(*) as count FROM products WHERE category_id = '$id'";
    $check_result = mysqli_query($conn, $check_query);
    $check_data = mysqli_fetch_assoc($check_result);
    
    if ($check_data['count'] > 0) {
        $_SESSION['error'] = "Cannot delete category - it's being used by products!";
    } else {
        $delete_query = "DELETE FROM categories WHERE id = '$id'";
        
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "Category deleted successfully!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
}

header("Location: view.php");
exit();
?>