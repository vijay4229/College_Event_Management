<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'participant') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$event_name = "";
$error_message = "";
$success_message = "";

// Verify that the event exists before showing the form
if ($event_id > 0) {
    $stmt = $conn->prepare("SELECT id, name FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $event_name = $row['name'];
    } else {
        // Event not found - redirect to browse with error
        header("Location: browse_events.php?error=event_not_found");
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = intval($_POST['event_id']);
    $branch = $_POST['branch'];
    $year = $_POST['year'];
    $college = $_POST['college'];

    // Verify event exists before registration
    $verify = $conn->prepare("SELECT id FROM events WHERE id = ?");
    $verify->bind_param("i", $event_id);
    $verify->execute();
    $verify_result = $verify->get_result();
    
    if ($verify_result->num_rows === 0) {
        $error_message = "Event does not exist. Please try again.";
    } else {
        // Check if already registered
        $check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
        $check->bind_param("ii", $user_id, $event_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows === 0) {
            try {
                // Not registered, insert with additional details
                $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id, branch, year, college) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $user_id, $event_id, $branch, $year, $college);
                
                if ($stmt->execute()) {
                    // Registration successful
                    header("Location: browse_events.php?success=registered");
                    exit();
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
            } catch (Exception $e) {
                $error_message = "Error: " . $e->getMessage();
            }
        } else {
            $error_message = "You are already registered for this event.";
        }
    }
}

// If no event_id or invalid, redirect to browse
if (empty($event_name)) {
    header("Location: browse_events.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register for Event | College Event Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Register for Event: <?= htmlspecialchars($event_name) ?></h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="register_event.php">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                
                <div class="form-group">
                    <label for="branch">Branch/Department:</label>
                    <input type="text" id="branch" name="branch" required>
                </div>
                
                <div class="form-group">
                    <label for="year">Year of Study:</label>
                    <select id="year" name="year" required>
                        <option value="">Select Year</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="5th Year">5th Year</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="college">College Name:</label>
                    <input type="text" id="college" name="college" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Register for Event</button>
                    <a href="browse_events.php" class="btn" style="background-color: #777;">Cancel</a>
                </div>
            </form>
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
