<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: alogin.php'); // Redirect to login page if not admin
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get employee ID and project name from the URL
$employee_id = $_GET['employee_id'];
$project_name = $_GET['project_name'];

// Fetch project details
$sql = "SELECT p.project_name, p.due_date, p.submission_date, 
        CONCAT(e.firstName, ' ', e.lastName) AS employee_name 
        FROM projects p 
        JOIN employee e ON p.employee_id = e.id 
        WHERE p.employee_id = ? AND p.project_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $employee_id, $project_name);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();

if (!$project) {
    echo "<script>
        alert('Invalid project or employee ID!');
        window.location.href = 'project_status.php';
    </script>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mark = $_POST['mark'];

    if ($mark < 5 || $mark > 10) {
        echo "<script>
            alert('Mark must be between 5 and 10!');
            window.history.back();
        </script>";
        exit();
    }

    // Update project mark
    $update_sql = "UPDATE projects 
                   SET mark = ?, status = 'Reviewed' 
                   WHERE employee_id = ? AND project_name = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iis", $mark, $employee_id, $project_name);

    if ($update_stmt->execute()) {
        echo "<script>
            alert('Mark assigned successfully!');
            window.location.href = 'project_status.php';
        </script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Project</title>
    <link rel="stylesheet" href="mark_project.css">
    <link rel="stylesheet" href="sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <h1>Project Mark Details</h1>
    <form method="POST">
        <label>Project Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($project['project_name']); ?>" readonly><br>

        <label>Employee Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($project['employee_name']); ?>" readonly><br>

        <label>Due Date:</label>
        <input type="date" value="<?php echo htmlspecialchars($project['due_date']); ?>" readonly><br>

        <label>Submission Date:</label>
        <input type="date" value="<?php echo htmlspecialchars($project['submission_date']); ?>" readonly><br>

        <label>Assign Mark (5-10):</label>
        <input type="number" name="mark" min="5" max="10" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
