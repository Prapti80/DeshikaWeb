<?php
session_start();

ini_set('display_errors', 0);
require_once '../config/database.php';
require_once '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get current user data with COALESCE for profile_image
$query = "SELECT *, COALESCE(profile_image, 'default.png') as profile_image FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $profile_image = $user['profile_image'];

    // Handle profile image upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../assets/images/profile/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $new_filename = "user_" . $user_id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is valid
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check !== false) {
            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed_types)) {
                // Validate file size (max 2MB)
                if ($_FILES['profile_image']['size'] <= 2097152) {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        // Delete old profile image if it's not the default
                        if ($profile_image !== 'default.png' && file_exists($target_dir . $profile_image)) {
                            @unlink($target_dir . $profile_image);
                        }
                        $profile_image = $new_filename;
                    } else {
                        $error = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $error = "File size must be less than 2MB.";
                }
            } else {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            }
        } else {
            $error = "File is not a valid image.";
        }
    }

    // Update user data
    $update_query = "UPDATE users SET 
                    name = '$name', 
                    phone = '$phone', 
                    address = '$address', 
                    profile_image = '$profile_image' 
                    WHERE id = $user_id";
    
    if (empty($error)) {
        if (mysqli_query($conn, $update_query)) {
            // Update session with new name if changed
            if ($_SESSION['user_name'] !== $name) {
                $_SESSION['user_name'] = $name;
            }
            
            header("Location: view.php?success=profile_updated");
            exit();
        } else {
            $error = "Error updating profile: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <?php include '../includes/header.php'; ?>
    <style>
        .profile-image-container {
            position: relative;
            display: inline-block;
        }
        .profile-image-container:hover .profile-image-overlay {
            opacity: 1;
        }
        .profile-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
            cursor: pointer;
        }
        .profile-image-overlay i {
            color: white;
            font-size: 1.5rem;
        }
        #imagePreview {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-pink text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Edit Profile</h4>
                            <a href="view.php" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to Profile
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" id="profileForm">
                            <div class="row mb-4 text-center">
                                <div class="col-12">
                                    <div class="profile-image-container mx-auto" style="width: 150px;">
                                        <img id="imagePreview" src="../assets/images/profile/<?php echo $user['profile_image']; ?>" 
                                             class="rounded-circle mb-3" 
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                        <div class="profile-image-overlay" onclick="document.getElementById('profile_image').click()">
                                            <i class="fas fa-camera"></i>
                                        </div>
                                    </div>
                                    <input type="file" id="profile_image" name="profile_image" 
                                           class="d-none" accept="image/*" onchange="previewImage(this)">
                                    <div class="text-muted small mt-2">Click image to change (Max 2MB)</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                    <small class="text-muted">Contact support to change email</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>"
                                           pattern="[0-9]{10}" title="10 digit phone number">
                                    <small class="text-muted">Format: 1234567890</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" 
                                           value="<?php echo !empty($user['dob']) ? htmlspecialchars($user['dob']) : ''; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3"><?php 
                                    echo htmlspecialchars($user['address']); 
                                ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea class="form-control" name="bio" rows="2" 
                                          placeholder="Tell us about yourself"><?php 
                                    echo htmlspecialchars($user['bio'] ?? ''); 
                                ?></textarea>
                                <small class="text-muted">Max 200 characters</small>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-pink">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone"]');
            if (phone.value && !/^\d{10}$/.test(phone.value)) {
                alert('Please enter a valid 10-digit phone number');
                e.preventDefault();
                return false;
            }
            
            const bio = document.querySelector('textarea[name="bio"]');
            if (bio.value.length > 200) {
                alert('Bio must be 200 characters or less');
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>