<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Set pagination variables
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get search keyword
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$search_keyword = trim($search_keyword);

// Fetch project records with sorting, pagination, and search
$sql = "SELECT p.employee_id, p.project_name, p.due_date, p.submission_date, p.mark, p.status, 
        CONCAT(e.firstName, ' ', e.lastName) AS employee_name 
        FROM projects p 
        JOIN employee e ON p.employee_id = e.id
        WHERE CONCAT(e.firstName, ' ', e.lastName) LIKE ? 
        ORDER BY 
            CASE 
                WHEN p.status = 'Pending' THEN 1
                ELSE 2
            END, 
            p.due_date ASC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search_keyword%";
$stmt->bind_param('sii', $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of records for pagination
$totalRecordsSql = "SELECT COUNT(*) AS total 
                    FROM projects p 
                    JOIN employee e ON p.employee_id = e.id
                    WHERE CONCAT(e.firstName, ' ', e.lastName) LIKE ?";
$totalStmt = $conn->prepare($totalRecordsSql);
$totalStmt->bind_param('s', $search_param);
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Status</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="project_status.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <h1>Project Status</h1>
    <div class="search-container">
        <form action="project_status.php" method="GET">
            <input type="text" name="search" placeholder="🔍 Search by Employee Name" value="<?php echo htmlspecialchars($search_keyword); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>Emp. ID</th>
                <th>Employee Name</th>
                <th>Project Name</th>
                <th>Due Date</th>
                <th>Submission Date</th>
                <th>Mark</th>
                <th>Status</th>
                <th>Option</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $markButtonClass = $row['mark'] ? "btn disabled" : "btn";
                    $markButtonDisabled = $row['mark'] ? "disabled" : "";
                    $statusClass = $row['status'] === 'Pending' ? 'status-pending' : 'status-submitted';
                    echo "<tr>
                        <td>{$row['employee_id']}</td>
                        <td>{$row['employee_name']}</td>
                        <td>{$row['project_name']}</td>
                        <td>{$row['due_date']}</td>
                        <td>{$row['submission_date']}</td>
                        <td>" . ($row['mark'] ?: 'Not Marked') . "</td>
                        <td><span class='status-badge {$statusClass}'>{$row['status']}</span></td>
                        <td>
                            <button class='{$markButtonClass}' onclick=\"openMarkForm('{$row['employee_id']}', '{$row['project_name']}')\" {$markButtonDisabled}>Mark</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search_keyword) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <script>
        function openMarkForm(employeeId, projectName) {
            const url = `mark_project.php?employee_id=${employeeId}&project_name=${encodeURIComponent(projectName)}`;
            window.location.href = url;
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
