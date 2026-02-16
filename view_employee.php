<?php
session_start();
include 'db_connection.php';
// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get the search query from the form, if present
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination setup
$limit = 9; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Offset for the SQL query

// Fetch total number of employees for pagination with the search condition
$total_query = "SELECT COUNT(*) AS total FROM employee WHERE firstName LIKE ? OR lastName LIKE ? OR id LIKE ?";
$search_term = "%$search%"; // Prepare search term for LIKE
$total_stmt = $conn->prepare($total_query);
$total_stmt->bind_param('sss', $search_term, $search_term, $search_term);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_employees = $total_row['total'];
$total_pages = ceil($total_employees / $limit);

// Fetch employees for the current page with the search condition
$sql = "SELECT * FROM employee WHERE firstName LIKE ? OR lastName LIKE ? OR id LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssi', $search_term, $search_term, $search_term, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="view_employee.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <h1>Employee Records</h1>

    <!-- Search Bar -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" placeholder="Search by Name or Employee ID" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <div class="card-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Ensure no XSS vulnerabilities
                $firstName = htmlspecialchars($row['firstName']);
                $lastName = htmlspecialchars($row['lastName']);
                $email = htmlspecialchars($row['email']);
                $contact = htmlspecialchars($row['contact']);
                $pic = $row['pic'] ? htmlspecialchars($row['pic']) : 'placeholder.png'; // Default image

                echo "<div class='employee-card'>
                    <img src='$pic' alt='Employee Picture'>
                    <h3>$firstName $lastName</h3>
                    <p><strong>Employee ID:</strong> {$row['id']}</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Contact:</strong> $contact</p>
                    <div class='card-buttons'>
                        <button class='see-more-btn' onclick=\"location.href='employee_detail.php?id={$row['id']}'\">See More</button>
                    </div>
                </div>";
            }
        } else {
            echo "<p>No records found</p>";
        }
        ?>
    </div>

    <!-- Pagination Section -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$total_stmt->close();
$conn->close();
?>
