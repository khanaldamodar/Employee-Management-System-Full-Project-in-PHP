<?php
require 'config.php'; // Make sure this connects to your database

$username = "kiran"; // Replace with your desired username
$password = "admin123"; // Replace with your desired password

// Hash the password for secure storage
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute the SQL insert
$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
