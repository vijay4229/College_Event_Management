<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Organizer Dashboard | College Event Management</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<!-- Main Content -->
<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> 👋</h2>
        <p class="role-tag">You're logged in as <strong>Organizer</strong></p>

        <div class="dashboard-actions">
            <a href="create_event.php" class="btn btn-block">➕ Create Event</a>
            <a href="manage_events.php" class="btn btn-block">🛠 Manage Events</a>
            <a href="view_registrations.php" class="btn btn-block">📄 View Registrations</a>
            <a href="../auth/logout.php" class="btn btn-block">🚪 Logout</a>
        </div>
    </div>
</div>

<footer>
    <p>© 2025. All Rights Reserved.</p>
    <p>Developed by Vijaykumar and Varun Bhat P</p>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
