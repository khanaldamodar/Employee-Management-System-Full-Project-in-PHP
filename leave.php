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

// Fetch leave requests
$sql = "SELECT l.id, l.employee_id, l.name, l.start_date, l.end_date, 
        l.total_days, l.reason, l.status 
        FROM leaves l 
        JOIN employee e ON l.employee_id = e.id";
$result = $conn->query($sql);

// Update leave status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === 'approve') {
        $updateSql = "UPDATE leaves SET status = 'Approved' WHERE id = $id";
    } elseif ($action === 'cancel') {
        $updateSql = "UPDATE leaves SET status = 'Cancelled' WHERE id = $id";
    }

    if ($conn->query($updateSql) === TRUE) {
        header("Location: employee_leave.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
