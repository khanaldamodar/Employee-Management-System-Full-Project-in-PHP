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
$employee_query = $conn->prepare("SELECT id, firstName, lastName, email, department, pic FROM employee WHERE email = ?");
$employee_query->bind_param("s", $email);
$employee_query->execute();
$employee_result = $employee_query->get_result();
$employee = $employee_result->fetch_assoc();
$employee_id = $employee['id'];

// Fetch pending project details
$projects_query = $conn->prepare("SELECT project_name, due_date, submission_date, mark, status FROM projects WHERE employee_id = ? AND status = 'Pending'");
$projects_query->bind_param("i", $employee_id);
$projects_query->execute();
$projects_result = $projects_query->get_result();

// Fetch pending leave details
$leaves_query = $conn->prepare("SELECT start_date, end_date, total_days, reason, status FROM leaves WHERE employee_id = ? AND status = 'Pending'");
$leaves_query->bind_param("i", $employee_id);
$leaves_query->execute();
$leaves_result = $leaves_query->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="employee_dashboard.css">
    <link rel="stylesheet" href="e_sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'e_sidebar.php'; ?>
    <div class="dashboard">
        <div class="main-content">
            <h1>Welcome back, <?php echo $employee['firstName']; ?></h1><br><br>

            <div class="section">
                <h2>Pending Projects</h2>
                <?php if ($projects_result->num_rows > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Due Date</th>
                                <th>Submission Date</th>
                                <th>Mark</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($project = $projects_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $project['project_name']; ?></td>
                                    <td><?php echo $project['due_date']; ?></td>
                                    <td><?php echo $project['submission_date'] ?? 'Not Submitted'; ?></td>
                                    <td><?php echo $project['mark'] ?? 'Pending'; ?></td>
                                    <td><?php echo $project['status']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p class="empty-message">No pending projects.</p>
                <?php } ?>
            </div>

            <div class="section">
                <h2>Pending Leave Requests</h2>
                <?php if ($leaves_result->num_rows > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Total Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($leave = $leaves_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $leave['start_date']; ?></td>
                                    <td><?php echo $leave['end_date']; ?></td>
                                    <td><?php echo $leave['total_days']; ?></td>
                                    <td><?php echo $leave['reason']; ?></td>
                                    <td><?php echo $leave['status']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p class="empty-message">No pending leave requests.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>