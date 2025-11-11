<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'organizer')) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if event ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid event ID";
    header("Location: manage_events.php");
    exit();
}

$event_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// First check if the event exists and belongs to the current user
$check_stmt = $conn->prepare("SELECT organizer_id FROM events WHERE id = ?");
$check_stmt->bind_param("i", $event_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Event not found";
    header("Location: manage_events.php");
    exit();
}

$event = $result->fetch_assoc();

// Check if user owns the event or is admin
if ($_SESSION['role'] !== 'admin' && $event['organizer_id'] !== $user_id) {
    $_SESSION['error'] = "You don't have permission to delete this event";
    header("Location: manage_events.php");
    exit();
}

// Delete related registrations first
$delete_reg_stmt = $conn->prepare("DELETE FROM registrations WHERE event_id = ?");
$delete_reg_stmt->bind_param("i", $event_id);
$delete_reg_stmt->execute();

// Now delete the event
$delete_stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$delete_stmt->bind_param("i", $event_id);

if ($delete_stmt->execute()) {
    $_SESSION['success'] = "Event deleted successfully";
} else {
    $_SESSION['error'] = "Error deleting event: " . $conn->error;
}

header("Location: manage_events.php");
exit(); 