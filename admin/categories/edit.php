<?php
require_once '../../includes/auth_check.php';
require_once '../../config/database.php';

if (!$_SESSION['is_admin']) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM categories WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$category = mysqli_fetch_assoc($result);

if (!$category) {
    header("Location: view.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = createSlug($name);
    
    $update_query = "UPDATE categories SET name = '$name', slug = '$slug' WHERE id = '$id'";
    
    if (mysqli_query($conn, $update_query)) {
        $_SESSION['success'] = "Category updated successfully!";
        header("Location: view.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
}

function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', "-", $slug);
    return $slug;
}
?>

<!-- Similar HTML structure to add.php, but with pre-filled values -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Same head as add.php -->
</head>
<body>
    <div class="wrapper d-flex">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="main-content flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Category</h2>
                <a href="view.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            
            <!-- Success/Error messages -->
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($category['name']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-pink">
                            <i class="fas fa-save me-1"></i> Update Category
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>