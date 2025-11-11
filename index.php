<!-- fms/index.php -->
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: dashboards/admin.php");
    } elseif ($role === 'organizer') {
        header("Location: dashboards/organizer.php");
    } else {
        header("Location: dashboards/participant.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College event Management</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-logo">
            <!-- Logo Placeholder -->
            <img src="assets/logo.png" alt="Logo" />
        </div>
        <div class="navbar-title">College Event Management</div>
    </div>

    <!-- Main Home Page -->
    <div class="home-container">
        <div class="home-card">
            <h1>Welcome!</h1>
            <p>Manage and participate in exciting college events all in one place.</p>
            <a href="auth/login.php" class="btn">Login to Continue</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025. All Rights Reserved.</p>
        <p>Developed by Vijaykumar and Varun Bhat P</p>
    </footer>
</body>
</html>
