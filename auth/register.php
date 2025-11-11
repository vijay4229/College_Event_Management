<?php
require_once '../includes/db.php';
session_start();

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Security check: Ensure role is only participant or organizer
    if ($role !== 'participant' && $role !== 'organizer') {
        $error = "Invalid role selected.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | College Event Management</title>
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
            <h2>Register</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="form-container" style="box-shadow: none;">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="participant">Participant</option>
                        <option value="organizer">Organizer</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Register</button>
                </div>
                <div class="form-group text-center">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
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
