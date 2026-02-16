<?php
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Pagination setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total employee count for pagination
$totalEmployeesQuery = $conn->query("SELECT COUNT(DISTINCT employee_id) AS total FROM projects");
$totalEmployeesCount = $totalEmployeesQuery->fetch_assoc()['total'];
$totalPages = ceil($totalEmployeesCount / $limit);

// Fetch top employees with highest total marks
$leaderboardQuery = $conn->query("
    SELECT e.id, CONCAT(e.firstName, ' ', e.lastName) AS name, COALESCE(SUM(p.mark), 0) AS total_marks
    FROM employee e
    LEFT JOIN projects p ON e.id = p.employee_id
    GROUP BY e.id
    ORDER BY total_marks DESC
    LIMIT $limit OFFSET $offset
");

// Fetch recent data
$recentEmployees = $conn->query("SELECT id, CONCAT(firstName, ' ', lastName) AS name, email, contact FROM employee ORDER BY id DESC LIMIT 5");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 
</head>
<body>

<?php include 'sidebar.php'; ?>



    <!-- Leaderboard Table -->
    <div class="dashboard-container">
    <h1>Admin Dashboard</h1>

    <!-- Parent Container for Side-by-Side Layout -->
    <div class="dashboard-grid">
        
        <!-- Employee Leaderboard Section -->
        <div class="leaderboard">
            <h3>Employee Leaderboard</h3>
            <table>
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Points (Total Marks)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $leaderboardQuery->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['total_marks']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search_keyword) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Recently Added Employees Section -->
        <div class="recent-employees">
            <h3>Recently Added Employees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recentEmployees->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div> 


</body>
</html>
