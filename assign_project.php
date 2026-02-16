<?php
session_start();
include 'db_connection.php';
// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error_message = ""; // Initialize the error message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "employee_management";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $employee_id = $_POST['employee_id'];
    $project_name = $_POST['project_name'];
    $due_date = $_POST['due_date'];

    // Validate Employee ID
    $stmt = $conn->prepare("SELECT id FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $error_message = "Invalid Employee ID. Please enter a valid ID.";
        $stmt->close();
    } else {
        $stmt->close();

        // Insert project details into the database
        $sql = "INSERT INTO projects (employee_id, project_name, due_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $employee_id, $project_name, $due_date);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: project_status.php'); // Redirect to project_status.php
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
            $stmt->close();
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign Project</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="sidebar.css">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
  <style>
    
    body {
      font-family: 'Quicksand', sans-serif;
      background: linear-gradient(135deg, #f0f4f8, #d9e4f5);
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #fff;
      padding: 20px;
      margin-left:15%;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.26);
      width: 400px;
    }

    h1 {
      font-size: 24px;
      margin-bottom: 20px;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      font-size: 18px;
    }

    input {
      width: 95%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: #28a745;
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
      border-radius: 4px;
    }

    button:hover {
      background-color: #218838;
    }

    #error-message {
      color: red;
      font-size: 18px;
      text-align: center;
      margin-top: 10px; /* Space between the form and the message */
    }
  </style>
  <script>
    // Client-side validation for the due date
    document.addEventListener("DOMContentLoaded", function () {
        const dueDateInput = document.getElementById("due_date");
        dueDateInput.min = new Date().toISOString().split("T")[0];

        const assignForm = document.getElementById("assignProjectForm");
        assignForm.addEventListener("submit", function (e) {
            const dueDate = new Date(dueDateInput.value);
            const today = new Date();

            // Validate that due date is not today or in the past
            if (dueDate <= today) {
                e.preventDefault();
                alert("Due date cannot be today or in the past.");
            }
        });
    });
  </script>
</head>
<body>
<?php include 'sidebar.php'; ?>
  <div class="container">
    <h1>Assign Project</h1>
    <form id="assignProjectForm" action="assign_project.php" method="POST">
      <label for="employee_id">Employee ID:</label>
      <input type="number" id="employee_id" name="employee_id" required>

      <label for="project_name">Project Name:</label>
      <input type="text" id="project_name" name="project_name" required>

      <label for="due_date">Due Date:</label>
      <input type="date" id="due_date" name="due_date" required>

      <button type="submit">Assign</button>
    </form>
    <p id="error-message">
      <?php if (!empty($error_message)) echo $error_message; ?>
    </p>
  </div>
</body>
</html>
