<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "employee_management");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total employees
$totalEmployees = $conn->query("SELECT COUNT(*) as total FROM employee")->fetch_assoc()['total'];

// Fetch active projects
$activeProjects = $conn->query("SELECT COUNT(*) as total FROM projects WHERE status = 'Active'")->fetch_assoc()['total'];

// Fetch pending leaves
$pendingLeaves = $conn->query("SELECT COUNT(*) as total FROM leaves WHERE status = 'Pending'")->fetch_assoc()['total'];

// Fetch project submissions
$projectSubmissions = $conn->query("SELECT COUNT(*) as total FROM projects WHERE submission_date IS NOT NULL")->fetch_assoc()['total'];

// Fetch recent activities (automated from projects and leaves)
$recentActivitiesQuery = "
    SELECT 
        e.firstName AS employee_name,
        CASE
            WHEN p.submission_date IS NOT NULL THEN CONCAT('Submitted Project: ', p.project_name)
            WHEN l.reason IS NOT NULL THEN CONCAT('Requested Leave: ', l.reason)
        END AS activity,
        COALESCE(p.submission_date, l.start_date) AS date,
        CASE
            WHEN p.status = 'Completed' THEN 'Completed'
            WHEN l.status = 'Pending' THEN 'Pending'
            ELSE 'Ongoing'
        END AS status
    FROM employee e
    LEFT JOIN projects p ON e.id = p.employee_id
    LEFT JOIN leaves l ON e.id = l.employee_id
    WHERE p.submission_date IS NOT NULL OR l.reason IS NOT NULL
    ORDER BY date DESC
    LIMIT 10;
";

$recentActivities = $conn->query($recentActivitiesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="sdidebar.css">
     <link rel="stylesheet" href="adminpanel.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="dashboard">
        <!-- Stats Section -->
        <div class="stats">
          <div class="stat-box">
            <h3>Total Employees</h3>
            <span><?php echo $totalEmployees; ?></span>
        </div>>
        <div class="stat-box">
          <h3>Active Projects</h3>
          <span><?php echo $activeProjects; ?></span>
    </div>
    <div class="stat-box">
        <h3>Pending Leaves</h3>
        <span><?php echo $pendingLeaves; ?></span>
    </div>
    <div class="stat-box">
        <h3>Project Submissions</h3>
        <span><?php echo $projectSubmissions; ?></span>
    </div>
</div>


        <!-- Recent Activities Section -->
        <div class="recent-activities">
            <h2>Recent Employee Activities</h2>
            <table>
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recentActivities->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['employee_name']; ?></td>
                            <td><?php echo $row['activity']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
