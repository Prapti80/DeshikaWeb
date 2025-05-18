<?php
require_once '../config/database.php';
header('Content-Type: application/json');

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $size = isset($_POST['size']) ? mysqli_real_escape_string($conn, $_POST['size']) : '';
    $color = isset($_POST['color']) ? mysqli_real_escape_string($conn, $_POST['color']) : '';
    
    // Check if product already exists in cart
    $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    if (!empty($size)) $check_query .= " AND size = '$size'";
    if (!empty($color)) $check_query .= " AND color = '$color'";
    
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update quantity if item exists
        $existing_item = mysqli_fetch_assoc($check_result);
        $new_quantity = $existing_item['quantity'] + $quantity;
        
        $update_query = "UPDATE cart SET quantity = $new_quantity WHERE id = ".$existing_item['id'];
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
        }
    } else {
        // Add new item to cart
        $insert_query = "INSERT INTO cart (user_id, product_id, quantity, size, color) 
                         VALUES ($user_id, $product_id, $quantity, '$size', '$color')";
        
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>