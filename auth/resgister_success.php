<?php
include '../includes/header.php';

if (!isset($_SESSION['registration_success'])) {
    header("Location: register.php");
    exit();
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5 text-center">
                    <h2 class="text-center mb-4" style="color: var(--dark-pink);">Registration Successful!</h2>
                    <p>We've sent a verification email to <?php echo $_SESSION['email']; ?>.</p>
                    <p>Please check your inbox and click the verification link to activate your account.</p>
                    <a href="login.php" class="btn btn-pink mt-3">Go to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
unset($_SESSION['registration_success']);
include '../includes/footer.php';
?>