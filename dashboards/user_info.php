<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_login(); // Ensure user is logged in

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Info | FMS</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <header class="navbar">
        <div class="logo"><img src="../assets/logo.png" alt="Logo"></div>
        <h2 class="title">Fest Management System</h2>
    </header>

    <div class="container">
        <h2>Your Profile</h2>
        <p><strong>Name:</strong> <?= e($user['name']) ?></p>
        <p><strong>Email:</strong> <?= e($user['email']) ?></p>
        <p><strong>Role:</strong> <?= ucfirst(e($user['role'])) ?></p>
        <p><strong>Joined On:</strong> <?= e(date('d M Y', strtotime($user['created_at']))) ?></p>

        <div style="margin-top: 20px;">
            <a href="reset_password.php" class="btn">Reset Password</a>
            <a href="../auth/logout.php" class="btn">Logout</a>
        </div>
    </div>

    <footer class="footer">
        <p>© 2025. All Rights Reserved.</p>
        <p>Developed by Vijaykumar and Varun Bhat P</p>
    </footer>
</body>
</html>
