<?php
session_start();
include 'db_connection.php';
// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch search keyword
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : "";

// Pagination setup
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch leave requests based on the search keyword and pagination, sorted by the most recent
if (!empty($search_keyword)) {
    $query = "SELECT * FROM leaves WHERE name LIKE ? ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $stmt = $conn->prepare($query);
    $search_term = "%" . $search_keyword . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM leaves ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);
}


// Fetch total number of records for pagination
$total_query = "SELECT COUNT(*) AS total FROM leaves";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// Check if a leave request is approved, denied, or canceled
if (isset($_GET['action']) && isset($_GET['leave_id'])) {
    $leave_id = intval($_GET['leave_id']); // Sanitize the ID
    $action = $_GET['action'];

    // Get the current status of the leave request
    $status_query = "SELECT status FROM leaves WHERE id = $leave_id";
    $status_result = mysqli_query($conn, $status_query);
    $status_row = mysqli_fetch_assoc($status_result);
    $current_status = $status_row['status'];

    // Prevent changes if the request is already approved or cancelled
    if ($current_status == 'approved' || $current_status == 'cancelled') {
        echo "<script>alert('This leave request has already been $current_status and cannot be changed.'); window.location.href='employee_leave.php';</script>";
        exit();
    }

    // Update leave status based on admin's action
    if ($action == 'approve') {
        $update_query = "UPDATE leaves SET status = 'approved' WHERE id = $leave_id";
    } elseif ($action == 'deny') {
        $update_query = "UPDATE leaves SET status = 'denied' WHERE id = $leave_id";
    } elseif ($action == 'cancel') {
        $update_query = "UPDATE leaves SET status = 'cancelled' WHERE id = $leave_id";
    } else {
        echo "<script>alert('Invalid action.'); window.location.href='employee_leave.php';</script>";
        exit;
    }

    // Execute the update query
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Leave request $action successfully.'); window.location.href='employee_leave.php';</script>";
    } else {
        echo "<script>alert('Error updating leave request.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Leave Requests</title>
    <link rel="stylesheet" href="sidebar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="employee_leave.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h1>Employee Leave Records</h1>

        <!-- Search bar -->
        <div class="search-container">
            <form action="employee_leave.php" method="GET">
                <input type="text" name="search" placeholder="Search by Employee Name" value="<?php echo htmlspecialchars($search_keyword); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Table to display leave records -->
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Total Days</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($leave = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $leave['employee_id'] . "</td>";
                        echo "<td>" . $leave['name'] . "</td>";
                        echo "<td>" . $leave['start_date'] . "</td>";
                        echo "<td>" . $leave['end_date'] . "</td>";
                        echo "<td>" . $leave['total_days'] . "</td>";
                        echo "<td>" . $leave['reason'] . "</td>";
                        echo "<td>";

                        // Show buttons based on current status
                        if ($leave['status'] == 'Pending') {
                            echo "<a href='employee_leave.php?action=approve&leave_id=" . $leave['id'] . "'><button class='approve-btn'>Approve</button></a>";
                            echo "<a href='employee_leave.php?action=cancel&leave_id=" . $leave['id'] . "'><button class='cancel-btn'>Cancel</button></a>";
                        } elseif ($leave['status'] == 'Approved') {
                            echo "<span class='status-approved'>Approved</span>";
                        } elseif ($leave['status'] == 'Cancelled') {
                            echo "<span class='status-cancelled'>Cancelled</span>";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No leave records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo "<a href='employee_leave.php?page=" . ($page - 1) . "&search=" . htmlspecialchars($search_keyword) . "'>&laquo; Previous</a>";
            }

            if ($page < $total_pages) {
                echo "<a href='employee_leave.php?page=" . ($page + 1) . "&search=" . htmlspecialchars($search_keyword) . "'>Next &raquo;</a>";
            }
            ?>
        </div>
    </div>
</body>
</html>