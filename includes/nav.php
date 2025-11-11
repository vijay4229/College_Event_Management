<?php
if (!isset($_SESSION)) session_start();
require_once 'functions.php';

require_login();
$role = $_SESSION['role'];
?>

<nav class="navbar">
    <button class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </button>
    
    <span class="navbar-title">College Event Management</span>

    <div class="sidebar">
        <?php if ($role === 'admin'): ?>
            <a href="../dashboards/admin.php">Dashboard</a>
            <a href="../dashboards/manage_events.php">Manage Events</a>
            <a href="../dashboards/manage_resources.php">Manage Resources</a>
            <a href="../dashboards/view_reports.php">Reports</a>
            <a href="../auth/logout.php">Logout</a>

        <?php elseif ($role === 'organizer'): ?>
            <a href="../dashboards/organizer.php">Dashboard</a>
            <a href="../dashboards/manage_events.php">Manage Events</a>
            <a href="../dashboards/manage_resources.php">Manage Resources</a>
            <a href="../dashboards/view_registrations.php">View Registrations</a>
            <a href="../auth/logout.php">Logout</a>

        <?php elseif ($role === 'participant'): ?>
            <a href="../dashboards/participant.php">Dashboard</a>
            <a href="../dashboards/register_event.php">Register Event</a>
            <a href="../auth/logout.php">Logout</a>

        <?php else: ?>
            <a href="../auth/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<div class="sidebar-overlay"></div>
