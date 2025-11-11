<?php
session_start();
require_once '../includes/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ../dashboards/admin.php");
                } elseif ($user['role'] === 'organizer') {
                    header("Location: ../dashboards/organizer.php");
                } else {
                    header("Location: ../dashboards/participant.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | College Event Management</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <span class="navbar-title">College Event Management</span>
        </nav>
    </header>

    <div class="home-container">
        <div class="auth-container">
            <h2>Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="form-container" style="box-shadow: none;">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Login</button>
                </div>
                <div class="form-group text-center">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2025. All Rights Reserved.</p>
        <p>Developed by Vijaykumar and Varun Bhat P</p>
    </footer>
</body>
</html>
