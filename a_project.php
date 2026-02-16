<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $employee_id = $_POST['employee_id'];
  $project_name = $_POST['project_name'];
  $due_date = $_POST['due_date'];

  // Check if Employee ID exists
  $sql = "SELECT * FROM employee WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $employee_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Insert project details into `projects` table
    $insert_sql = "INSERT INTO projects (employee_id, project_name, due_date) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iss", $employee_id, $project_name, $due_date);

    if ($insert_stmt->execute()) {
      echo "<script>
              alert('Project Assigned Successfully!');
              window.location.href = 'project_status.php';
            </script>";
    } else {
      echo "<script>
              alert('Error Assigning Project.');
              window.location.href = 'assign_project.php';
            </script>";
    }
  } else {
    echo "<script>
            alert('Employee ID does not exist.');
            window.location.href = 'assign_project.php';
          </script>";
  }

  $stmt->close();
  $conn->close();
}
?>
