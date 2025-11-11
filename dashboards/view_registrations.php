<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'organizer')) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

// SQL query - different for admin and organizer
if ($is_admin) {
    $sql = "
        SELECT r.id AS reg_id, u.name AS participant_name, u.email, e.name AS event_name, e.date, 
               r.registered_at, r.branch, r.year, r.college,
               CONCAT('Organizer: ', o.name) as organizer_info
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        JOIN events e ON r.event_id = e.id
        JOIN users o ON e.organizer_id = o.id
        ORDER BY e.date, r.registered_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
} else {
    $sql = "
        SELECT r.id AS reg_id, u.name AS participant_name, u.email, e.name AS event_name, e.date, 
               r.registered_at, r.branch, r.year, r.college
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        JOIN events e ON r.event_id = e.id
        WHERE e.organizer_id = ?
        ORDER BY e.date, r.registered_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Registrations | College Event Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Event Registrations</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Participant Name</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Year</th>
                        <th>College</th>
                        <th>Event</th>
                        <th>Event Date</th>
                        <th>Registration Time</th>
                        <?php if ($is_admin): ?>
                        <th>Organizer</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['participant_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['branch'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['year'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['college'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['date'])) ?></td>
                            <td><?= date('M d, Y H:i', strtotime($row['registered_at'])) ?></td>
                            <?php if ($is_admin): ?>
                            <td><?= htmlspecialchars($row['organizer_info']) ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No registrations found<?= $is_admin ? '.' : ' for your events.' ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>© 2025. All Rights Reserved.</p>
    <p>Developed by Vijaykumar and Varun Bhat P</p>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
