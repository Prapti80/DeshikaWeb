<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = createSlug($name); // Function to create slug from name
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $discount_price = mysqli_real_escape_string($conn, $_POST['discount_price']);
    $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
    $colors = mysqli_real_escape_string($conn, $_POST['colors']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle file upload
    $target_dir = "../../assets/images/products/";
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $_SESSION['error'] = "File is not an image.";
        header("Location: add.php");
        exit();
    }
    
    // Check file size (max 2MB)
    if ($_FILES["image"]["size"] > 2000000) {
        $_SESSION['error'] = "Sorry, your file is too large (max 2MB).";
        header("Location: add.php");
        exit();
    }
    
    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        header("Location: add.php");
        exit();
    }
    
    // Generate unique filename
    $new_filename = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;
    
    // Try to upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert product into database
        $query = "INSERT INTO products 
                  (category_id, name, slug, description, price, discount_price, sizes, colors, image, stock, is_featured) 
                  VALUES 
                  ('$category_id', '$name', '$slug', '$description', '$price', '$discount_price', '$sizes', '$colors', '$new_filename', '$stock', '$is_featured')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Product added successfully!";
            header("Location: view.php");
            exit();
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
            header("Location: add.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        header("Location: add.php");
        exit();
    }
} else {
    header("Location: add.php");
    exit();
}

// Function to create slug from product name
function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', "-", $slug);
    return $slug;
}
?>