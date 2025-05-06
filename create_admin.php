<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require 'db_connect.php';

// Check if connection is okay
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$username = 'admin';
$password = 'admin123';

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare insert statement
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");

if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}

$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully.";
} else {
    echo "❌ Error executing: " . $stmt->error;
}
?>
