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

// Handle resource addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_resource'])) {
    $resource_name = trim($_POST['resource_name']);
    $resource_type = trim($_POST['resource_type']);
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
    $quantity = (int)$_POST['quantity'];
    
    if (!empty($resource_name) && !empty($resource_type)) {
        $stmt = $conn->prepare("INSERT INTO resources (resource_name, resource_type, event_id, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $resource_name, $resource_type, $event_id, $quantity);
        
        if ($stmt->execute()) {
            $success = "Resource added successfully!";
        } else {
            $message = "Error adding resource: " . $conn->error;
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Handle resource deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $resource_id = (int)$_GET['delete'];
    
    // Delete the resource
    $query = "DELETE FROM resources WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $resource_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success = "Resource deleted successfully!";
    } else {
        $message = "Failed to delete resource.";
    }
}

// Fetch resources
$sql = "SELECT r.*, e.name as event_name 
        FROM resources r 
        LEFT JOIN events e ON r.event_id = e.id 
        ORDER BY r.resource_name";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Fetch events for dropdown
$events_sql = "SELECT id, name FROM events";
if (!$is_admin) {
    $events_sql .= " WHERE organizer_id = ?";
}
$events_stmt = $conn->prepare($events_sql);
if (!$is_admin) {
    $events_stmt->bind_param("i", $user_id);
}
$events_stmt->execute();
$events_result = $events_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Resources | Fest Management System</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Manage Resources</h2>
        
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

        <!-- Add Resource Form -->
        <h3>Add New Resource</h3>
        <form method="POST" class="form-container">
            <div class="form-group">
                <label for="resource_name">Resource Name:</label>
                <input type="text" id="resource_name" name="resource_name" required>
            </div>

            <div class="form-group">
                <label for="resource_type">Resource Type:</label>
                <select id="resource_type" name="resource_type" required>
                    <option value="Projector">Projector</option>
                    <option value="Hall">Hall</option>
                    <option value="Sound System">Sound System</option>
                    <option value="Chairs">Chairs</option>
                    <option value="Tables">Tables</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="event_id">Associated Event (Optional):</label>
                <select id="event_id" name="event_id">
                    <option value="">-- Select Event --</option>
                    <?php while ($event = $events_result->fetch_assoc()): ?>
                        <option value="<?= $event['id'] ?>"><?= htmlspecialchars($event['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity Available:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required>
            </div>

            <div class="form-group">
                <button type="submit" name="add_resource" class="btn">Add Resource</button>
            </div>
        </form>

        <!-- Resources List -->
        <h3>Available Resources</h3>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Event</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['resource_name']) ?></td>
                            <td><?= htmlspecialchars($row['resource_type']) ?></td>
                            <td>
                                <?= $row['event_name'] ? htmlspecialchars($row['event_name']) : '<em>Not assigned</em>' ?>
                            </td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this resource?')" class="btn btn-small">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No resources found.</div>
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