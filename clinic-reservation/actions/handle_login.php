<?php
session_start();
require '../config/db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'middle_initial' => $user['middle_initial'],
        'role' => $user['role']
    ];

    header("Location: ../public/" . ($user['role'] == 'admin' ? "admin.php" : "dashboard.php"));
    exit;
} else {
    // 👇 Redirect back to login with error
    header("Location: ../public/index.php?error=1");
    exit;
}
?>
