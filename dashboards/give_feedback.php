<?php
session_start();
// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Include the database and functions
include '../includes/db.php';
include '../includes/functions.php';

$user_id = $_SESSION['user_id'];
$message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['feedback_message'])) {
        $feedback_message = $_POST['feedback_message'];

        // Prepare and execute the SQL query to insert feedback
        $stmt = $con->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $feedback_message);

        if ($stmt->execute()) {
            $message = "Feedback submitted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <?php include '../includes/sidebar.php'; // Your site's sidebar ?>

    <div class="content">
        <h2>Submit Feedback</h2>
        <p>We value your feedback. Please let us know your thoughts!</p>

        <form action="give_feedback.php" method="POST">
            <label for="feedback_message">Your Feedback:</label>
            <textarea name="feedback_message" id="feedback_message" rows="10" required></textarea>

            <input type="submit" value="Submit Feedback" class="btn">
        </form>

        <?php
        // Display the success/error message if there is one
        if ($message) {
            echo "<p class='message'>$message</p>";
        }
        ?>
    </div>

</body>
</html>