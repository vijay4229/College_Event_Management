<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

try {
    // Total events count
    $total_events_sql = "SELECT COUNT(*) as total FROM events";
    $total_events_result = $conn->query($total_events_sql);
    $total_events = $total_events_result->fetch_assoc()['total'];

    // Total participants count
    $total_participants_sql = "SELECT COUNT(DISTINCT user_id) as total FROM registrations";
    $total_participants_result = $conn->query($total_participants_sql);
    $total_participants = $total_participants_result->fetch_assoc()['total'];

    // Total registrations count
    $total_registrations_sql = "SELECT COUNT(*) as total FROM registrations";
    $total_registrations_result = $conn->query($total_registrations_sql);
    $total_registrations = $total_registrations_result->fetch_assoc()['total'];

    // Events by category
    $category_sql = "SELECT COALESCE(category, 'Uncategorized') as category, COUNT(*) as total_events FROM events GROUP BY category";
    $category_result = $conn->query($category_sql);

    // Participants by event
    $participant_sql = "
        SELECT e.name as event_name, COUNT(r.user_id) as total_participants
        FROM events e
        LEFT JOIN registrations r ON e.id = r.event_id
        GROUP BY e.id, e.name
        ORDER BY total_participants DESC
    ";
    $participant_result = $conn->query($participant_sql);

    // Recent registrations with user email
    $recent_registrations_sql = "
        SELECT u.name as participant_name, u.email, e.name as event_name, 
               r.branch, r.year, r.college, r.registered_at
        FROM registrations r
        JOIN events e ON r.event_id = e.id
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.registered_at DESC
        LIMIT 10
    ";
    $recent_registrations_result = $conn->query($recent_registrations_sql);
} catch (mysqli_sql_exception $e) {
    // If there's an error with any query, we'll show a simplified report
    $error_message = "Some reports are temporarily unavailable. Please try again later.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports | College Event Management</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            min-width: 200px;
            box-shadow: var(--shadow-small);
            border-left: 4px solid var(--primary-color);
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }
        .report-section {
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-small);
        }
        .report-section h3 {
            color: var(--primary-color);
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--gray-light);
        }
    </style>
</head>
<body>

<?php include '../includes/nav.php'; ?>

<div class="content-with-sidebar">
    <div class="home-container">
        <h2>Reports Dashboard</h2>
        
        <div class="dashboard-actions">
            <a href="admin.php" class="btn">🏠 Back to Dashboard</a>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Overview Statistics -->
        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Events</h3>
                <div class="stat-number"><?php echo isset($total_events) ? $total_events : '0'; ?></div>
            </div>
            <div class="stat-box">
                <h3>Total Participants</h3>
                <div class="stat-number"><?php echo isset($total_participants) ? $total_participants : '0'; ?></div>
            </div>
            <div class="stat-box">
                <h3>Total Registrations</h3>
                <div class="stat-number"><?php echo isset($total_registrations) ? $total_registrations : '0'; ?></div>
            </div>
        </div>

        <!-- Events by Category -->
        <?php if (isset($category_result) && $category_result->num_rows > 0): ?>
        <div class="report-section">
            <h3>Events by Category</h3>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Events</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $category_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo $row['total_events']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Participation by Event -->
        <?php if (isset($participant_result) && $participant_result->num_rows > 0): ?>
        <div class="report-section">
            <h3>Participation by Event</h3>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Total Participants</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $participant_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td><?php echo $row['total_participants']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Recent Registrations -->
        <?php if (isset($recent_registrations_result) && $recent_registrations_result->num_rows > 0): ?>
        <div class="report-section">
            <h3>Recent Registrations</h3>
            <table>
                <thead>
                    <tr>
                        <th>Participant</th>
                        <th>Event</th>
                        <th>Branch</th>
                        <th>Year</th>
                        <th>College</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recent_registrations_result->fetch_assoc()): ?>
                        <tr>
                            <td title="<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['participant_name'] ?? 'User'); ?></td>
                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['branch'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['year'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['college'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($row['registered_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
