<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // First delete order items
    $delete_items = "DELETE FROM order_items WHERE order_id = '$id'";
    mysqli_query($conn, $delete_items);
    
    // Then delete the order
    $delete_order = "DELETE FROM orders WHERE id = '$id'";
    
    if (mysqli_query($conn, $delete_order)) {
        $_SESSION['success'] = "Order deleted successfully!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}

header("Location: view.php");
exit();
?>