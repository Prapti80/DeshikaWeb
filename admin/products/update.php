<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
    $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
    $colors = mysqli_real_escape_string($conn, $_POST['colors']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $current_image = mysqli_real_escape_string($conn, $_POST['current_image']);
    
    // Handle file upload if a new image was provided
    $image_name = $current_image;
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../../assets/images/products/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
            header("Location: edit.php?id=$product_id");
            exit();
        }
        
        // Check file size (max 2MB)
        if ($_FILES["image"]["size"] > 2000000) {
            $_SESSION['error'] = "Sorry, your file is too large (max 2MB).";
            header("Location: edit.php?id=$product_id");
            exit();
        }
        
        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: edit.php?id=$product_id");
            exit();
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Try to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if it's not the default one
            if ($current_image && file_exists($target_dir . $current_image)) {
                unlink($target_dir . $current_image);
            }
            $image_name = $new_filename;
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: edit.php?id=$product_id");
            exit();
        }
    }
    
   // Update product in database
    $query = "UPDATE products SET 
              category_id = '$category_id', 
              name = '$name', 
              slug = '$slug',
              description = '$description', 
              price = '$price', 
              discount_price = '$discount_price', 
              sizes = '$sizes', 
              colors = '$colors', 
              image = '$image_name', 
              stock = '$stock', 
              is_featured = '$is_featured',
              updated_at = NOW()
              WHERE id = $product_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Product updated successfully!";
        header("Location: view.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header("Location: edit.php?id=$product_id");
        exit();
    }
} else {
    header("Location: view.php");
    exit();
}
?>