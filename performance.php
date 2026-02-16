<?php
session_start();
include('db_connection.php'); // Include your database connection file

// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
    header('Location: userlogin.html');
    exit;
}

$email = $_SESSION['email'];

// Fetch employee details
$employee_query = $conn->prepare("SELECT id, firstName, lastName, email, department FROM employee WHERE email = ?");
$employee_query->bind_param("s", $email);
$employee_query->execute();
$employee_result = $employee_query->get_result();
$employee = $employee_result->fetch_assoc();
$employee_id = $employee['id'];

// Calculate Project Completion Rate
$completed_projects_query = $conn->prepare("SELECT COUNT(*) as completed_projects FROM projects WHERE employee_id = ? AND status = 'Completed'");
$completed_projects_query->bind_param("i", $employee_id);
$completed_projects_query->execute();
$completed_projects_result = $completed_projects_query->get_result();
$completed_projects = $completed_projects_result->fetch_assoc()['completed_projects'];

$total_projects_query = $conn->prepare("SELECT COUNT(*) as total_projects FROM projects WHERE employee_id = ?");
$total_projects_query->bind_param("i", $employee_id);
$total_projects_query->execute();
$total_projects_result = $total_projects_query->get_result();
$total_projects = $total_projects_result->fetch_assoc()['total_projects'];

$completion_rate = $total_projects > 0 ? ($completed_projects / $total_projects) * 100 : 0;

// Calculate Average Marks
$marks_query = $conn->prepare("SELECT AVG(mark) as average_marks FROM projects WHERE employee_id = ?");
$marks_query->bind_param("i", $employee_id);
$marks_query->execute();
$marks_result = $marks_query->get_result();
$average_marks = $marks_result->fetch_assoc()['average_marks'];

// Calculate Performance Score (e.g., 50% completion rate + 50% average marks)
$performance_score = ($completion_rate * 0.5) + ($average_marks * 0.5);

// Insert or Update performance in the performance table
$performance_query = $conn->prepare("INSERT INTO performance (employee_id, project_completion_rate, average_marks, performance_score) 
                                     VALUES (?, ?, ?, ?)
                                     ON DUPLICATE KEY UPDATE project_completion_rate = ?, average_marks = ?, performance_score = ?");
$performance_query->bind_param("idddddd", $employee_id, $completion_rate, $average_marks, $performance_score, $completion_rate, $average_marks, $performance_score);
$performance_query->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Performance</title>
    <link rel="stylesheet" href="employee_performance.css">
    <style>
        /* Simple CSS for performance section */
        .performance-section {
            margin-top: 30px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .performance-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .performance-section p {
            font-size: 1.2rem;
        }
        .progress-bar {
            width: 100%;
            background-color: #f1f1f1;
            border-radius: 8px;
            height: 20px;
        }
        .progress-bar div {
            height: 100%;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="performance-section">
        <h2>Your Performance</h2>
        <p><strong>Project Completion Rate:</strong> <?php echo number_format($completion_rate, 2); ?>%</p>
        <div class="progress-bar">
            <div style="width: <?php echo number_format($completion_rate, 2); ?>%; background-color: #4CAF50;"></div>
        </div>

        <p><strong>Average Marks:</strong> <?php echo number_format($average_marks, 2); ?> / 10</p>

        <p><strong>Your Performance Score:</strong> <?php echo number_format($performance_score, 2); ?> / 10</p>
    </div>
</body>
</html>
