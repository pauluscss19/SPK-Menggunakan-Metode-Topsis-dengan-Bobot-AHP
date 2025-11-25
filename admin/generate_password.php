<?php
// File: generate_password.php
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>Hash: <strong>$hash</strong></p>";
echo "<hr>";
echo "<p>Copy hash di atas, lalu jalankan query ini di phpMyAdmin:</p>";
echo "<pre>";
echo "UPDATE admin SET password = '$hash' WHERE username = 'admin';";
echo "</pre>";
?>
