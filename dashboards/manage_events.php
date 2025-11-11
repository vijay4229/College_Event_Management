<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'organizer')) {
    header("Location: ../auth/login.php");
    exit();
}

$organizer_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// If admin, show all events, if organizer show only their events
$query = ($role === 'admin') 
    ? "SELECT e.*, u.name as organizer_name FROM events e LEFT JOIN users u ON e.organizer_id = u.id"
    : "SELECT * FROM events WHERE organizer_id = ?";

$stmt = $conn->prepare($query);
if ($role !== 'admin') {
    $stmt->bind_param("i", $organizer_id);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events | Fest Management System</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Manage Events</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Category</th>
                    <?php if ($role === 'admin'): ?>
                        <th>Organizer</th>
                    <?php endif; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <?php if ($role === 'admin'): ?>
                        <td><?= htmlspecialchars($row['organizer_name'] ?? 'Unknown') ?></td>
                    <?php endif; ?>
                    <td class="actions">
                        <a href="edit_events.php?id=<?= $row['id'] ?>" class="btn btn-small">Edit</a>
                        <a href="delete_event.php?id=<?= $row['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.');"
                           class="btn btn-small btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <p>© 2025. All Rights Reserved.</p>
    <p>Developed by Vijaykumar and Varun Bhat P</p>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
