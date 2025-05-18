<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['id']);
    
    // First get the image name to delete the file
    $query = "SELECT image FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
    
    if ($product) {
        // Delete the product image file
        $image_path = "../../assets/images/products/" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Delete the product from database
        $delete_query = "DELETE FROM products WHERE id = $product_id";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting product: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Product not found";
    }
    
    header("Location: view.php");
    exit();
} else {
    header("Location: view.php");
    exit();
}
?>