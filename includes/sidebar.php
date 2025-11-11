<?php
if (!isset($_SESSION)) {
    session_start();
}
// Set role to 'participant' by default if no user is found
$role = $_SESSION['role'] ?? 'participant'; 

// --- This part figures out the correct dashboard link for EACH role ---
$dashboard_link = '../dashboards/participant.php'; // Default for participant
if ($role === 'admin') {
    $dashboard_link = '../dashboards/admin.php';
} elseif ($role === 'organizer') {
    $dashboard_link = '../dashboards/organizer.php';
}
?>

<div class="sidebar">
    <a href="<?php echo $dashboard_link; ?>">🏠 Dashboard</a>

    <?php if ($role === 'admin'): ?>
        <a href="../dashboards/create_event.php">➕ Create Event</a>
        <a href="../dashboards/manage_events.php">🛠 Manage Events</a>
        <a href="../dashboards/view_reports.php">📊 View Reports</a>

    <?php elseif ($role === 'organizer'): ?>
        <a href="../dashboards/create_event.php">➕ Create Event</a>
        <a href="../dashboards/manage_events.php">🛠 Manage Events</a>
        <a href="../dashboards/view_registrations.php">📄 View Registrations</a>

    <?php elseif ($role === 'participant'): ?>
        <a href="../dashboards/browse_events.php">🎉 Browse Events</a>
        <a href="../dashboards/user_info.php">👤 My Profile</a>
        
        <a href="../dashboards/give_feedback.php">💬 Give Feedback</a>
        
    <?php endif; ?>

    <a href="../auth/logout.php">🚪 Logout</a>
</div>