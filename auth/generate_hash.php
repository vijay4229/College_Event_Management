<?php
// Replace 'your_password_here' with the password you want to use
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "<br>Use this hash value in your SQL query.";
?> 