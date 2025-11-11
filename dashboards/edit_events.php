<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'organizer')) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';
$message = '';
$success = '';

// Check if event ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_events.php");
    exit();
}

$event_id = (int)$_GET['id'];

// Check if user has permission to edit this event
if (!$is_admin) {
    $check_sql = "SELECT id FROM events WHERE id = ? AND organizer_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $event_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        // Not authorized to edit this event
        header("Location: manage_events.php");
        exit();
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $category = trim($_POST['category']);
    $max_participants = (int)$_POST['max_participants'];

    if (!empty($name) && !empty($date)) {
        $sql = "UPDATE events SET name = ?, description = ?, date = ?, category = ?, max_participants = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiii", $name, $description, $date, $category, $max_participants, $event_id);
        
        if ($stmt->execute()) {
            $success = "Event updated successfully!";
        } else {
            $message = "Error updating event: " . $conn->error;
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch the event data
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_events.php");
    exit();
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Event | College Event Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Edit Event</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($event['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($event['date']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($event['category']) ?>" required>
            </div>

            <div class="form-group">
                <label for="max_participants">Maximum Participants:</label>
                <input type="number" id="max_participants" name="max_participants" min="1" value="<?= $event['max_participants'] ?? 100 ?>">
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Update Event</button>
                <a href="manage_events.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<footer>
    <p>© 2025. All Rights Reserved.</p>
    <p>Developed by Vijaykumar and Varun Bhat P</p>
</footer>

<script src="../assets/js/main.js"></script>
</body>
</html>
