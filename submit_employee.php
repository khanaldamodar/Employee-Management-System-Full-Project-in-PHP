<?php
$servername = "localhost"; // Change if your server is different
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "employee_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO employees (name, department, designation, email, phone, date_of_joining, salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $name, $department, $designation, $email, $phone, $date_of_joining, $salary);

// Set parameters and execute
$name = $_POST['name'];
$department = $_POST['department'];
$designation = $_POST['designation'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$date_of_joining = $_POST['date_of_joining'];
$salary = $_POST['salary'];

if ($stmt->execute()) {
    echo "New employee added successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>