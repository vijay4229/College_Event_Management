<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'participant') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = "";
$error_message = "";

// Handle success/error messages
if (isset($_GET['success']) && $_GET['success'] === 'registered') {
    $success_message = "You have successfully registered for the event.";
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'event_not_found') {
        $error_message = "The event you selected could not be found.";
    }
}

// Fetch all events with a LEFT JOIN to check if this user is already registered
$sql = "
    SELECT e.id, e.name, e.description, e.date, e.category,
           IF(r.id IS NULL, 0, 1) AS is_registered
    FROM events e
    LEFT JOIN registrations r ON e.id = r.event_id AND r.user_id = ?
    ORDER BY e.date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Events | College Event Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Browse Events</h2>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-actions">
            <a href="participant.php" class="btn">🏠 Back to Dashboard</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td>
                                <?php if ($row['is_registered']): ?>
                                    <span class="badge success">✅ Registered</span>
                                <?php else: ?>
                                    <a href="register_event.php?event_id=<?= $row['id'] ?>" class="btn btn-small">Register</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No events available.</p>
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
