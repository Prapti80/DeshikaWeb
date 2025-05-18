<?php
include '../includes/header.php';
// Display verification success message
if (isset($_SESSION['verification_success'])) {
    echo '<div class="alert alert-success text-center">'
        . $_SESSION['verification_success']['message']
        . '</div>';
    unset($_SESSION['verification_success']);
}
// Display error messages if any
if (isset($_SESSION['login_error'])) {
    echo '<div class="alert alert-danger text-center">'.$_SESSION['login_error'].'</div>';
    unset($_SESSION['login_error']);
}
// Registration success message
if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    echo '<div class="alert alert-success text-center">Registration successful! Please check your email for verification.</div>';
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: var(--dark-pink);">Login to Your Account</h2>
                    
                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="text-end mt-2">
                                <a href="forgot_password.php" style="color: var(--dark-pink);">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="showPassword">
                            <label class="form-check-label" for="showPassword">Show Password</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-pink btn-lg">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php" style="color: var(--dark-pink);">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Show password toggle
document.getElementById('showPassword').addEventListener('change', function() {
    const passwordField = document.getElementById('password');
    passwordField.type = this.checked ? 'text' : 'password';
});
</script>
<?php
include '../includes/footer.php';
?>