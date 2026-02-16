<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
  header("Location: login.php");
  exit();
}

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

$message = "";

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_SESSION['employee_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch current password from the database
    $stmt = $conn->prepare("SELECT password FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $current_password) {
            if ($new_password === $confirm_password) {
                // Update password
                $stmt = $conn->prepare("UPDATE employee SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password, $employee_id);
                if ($stmt->execute()) {
                    $message = "Password updated successfully!";
                } else {
                    $message = "Error updating password. Please try again.";
                }
            } else {
                $message = "New password and confirm password do not match.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    } else {
        $message = "Employee not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="e_sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
         *{
            font-family: 'Open Sans', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f0f4f8, #d9e4f5);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-left:13%;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 30px;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size:20px;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background:rgb(43, 66, 82);
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            
        }
        button:hover {
            background: #2980b9;
        }
        .message {
            text-align: center;
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include 'e_sidebar.php'; ?>
    <div class="container">
        <h2>Change Password</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Change Password</button>
            <div class="message"><?php echo $message; ?></div>
        </form>
    </div>
</body>
</html>
