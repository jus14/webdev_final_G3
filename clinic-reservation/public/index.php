<?php
session_start();
require '../config/db.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$loginError = isset($_GET['error']) && $_GET['error'] == 1;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="index-register.css">
</head>
<body>
<?php require 'components/header.php'; ?>

    <div class="page-content">
        <div class="container">
            <h2 style="color: white;";>Login</h2>

<?php if ($loginError): ?>
    <p style="color: red;">Invalid email or password. Please try again.</p>
<?php endif; ?>

<form action="../actions/handle_login.php" method="POST">

                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <p><a href="register.php">Don't have an account? Register</a></p>
            <p><a href="forgot_password.php">Forgot your password?</a></p>
            
        </div>
    </div>
<?php require 'components/footer.php'; ?>
</body>
</html>