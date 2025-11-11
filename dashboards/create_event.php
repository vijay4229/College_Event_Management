<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'organizer')) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $category = trim($_POST['category']);
    $organizer_id = $_SESSION['user_id'];

    if (!empty($name) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO events (name, description, date, category, organizer_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $description, $date, $category, $organizer_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Event created successfully!";
            header("Location: manage_events.php");
            exit();
        } else {
            $message = "Failed to create event: " . $conn->error;
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Event | Fest Management System</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Create Event</h2>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <div class="form-group">
                <label for="name">Event Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" id="category" name="category">
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Create Event</button>
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
