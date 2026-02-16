<?php
session_start();
include 'db_connection.php';

// Ensure that only logged-in employees can access this page
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
  header("Location: login.php");
  exit();
}

// Fetch the employee's details using the session email
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$sql = "SELECT id AS employee_id, CONCAT(firstName, ' ', lastName) AS name FROM employee WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Employee details not found.");
}

$employee_id = $employee['employee_id'];
$name = $employee['name'];

// Handle the leave application form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that all necessary fields are set and non-empty
    if (isset($_POST['leave_type'], $_POST['reason'], $_POST['start_date'], $_POST['end_date'])) {
        $leave_type = $_POST['leave_type'];
        $reason = $_POST['reason'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        // Calculate total days based on start and end dates
        $total_days = (strtotime($end_date) - strtotime($start_date)) / 86400 + 1;
        $status = "Pending"; // Initial status set to "Pending"

        // Insert leave request into the database
        $sql = "INSERT INTO leaves (employee_id, name, leave_type, start_date, end_date, total_days, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isssisss', $employee_id, $name, $leave_type, $start_date, $end_date, $total_days, $reason, $status);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=true");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "All fields are required.";
    }
}

// Fetch employee's leave history
$sql = "SELECT * FROM leaves WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $employee_id);
$stmt->execute();
$leave_history = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <link rel="stylesheet" href="e_sidebar.css">
    <link rel="stylesheet" href="apply_leave.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'e_sidebar.php'; ?>
<div class="container">
    <h1>Apply Leave</h1>

    <!-- Display Success or Error Message -->
    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Apply Leave Form -->
    <form method="POST" action="">
        <label for="leave_type">Leave Type</label>
        <select id="leave_type" name="leave_type" required>
            <option value="Sick Leave">Sick Leave</option>
            <option value="Emergency Leave">Emergency Leave</option>
            <option value="Study Leave">Study Leave</option>
            <option value="Maternity Leave">Maternity Leave</option>
            <option value="Other">Other</option>
        </select>

        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" name="start_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">

        <label for="end_date">End Date</label>
        <input type="date" id="end_date" name="end_date" required>

        <label for="reason">Reason</label>
        <textarea id="reason" name="reason" rows="2" required></textarea>

        <button type="submit">Submit Leave Request</button>
    </form>

    <!-- View Leave History Button -->
    <button id="viewLeaveHistoryBtn">View Leave History</button>

    <!-- Leave History Modal -->
    <div id="leaveHistoryModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeLeaveHistoryModal();">&times;</span>
            <h2>Leave History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Leave ID</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($leave_history->num_rows > 0): ?>
                        <?php while ($row = $leave_history->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['leave_type']) ?></td>
                                <td><?= htmlspecialchars($row['start_date']) ?></td>
                                <td><?= htmlspecialchars($row['end_date']) ?></td>
                                <td><?= htmlspecialchars($row['total_days']) ?></td>
                                <td><?= htmlspecialchars($row['reason']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No leave history found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('viewLeaveHistoryBtn').addEventListener('click', function () {
        document.getElementById('leaveHistoryModal').style.display = 'block';
    });

    function closeLeaveHistoryModal() {
        document.getElementById('leaveHistoryModal').style.display = 'none';
    }

    // Close the modal if the user clicks outside it
    window.onclick = function (event) {
        const modal = document.getElementById('leaveHistoryModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
</script>

</body>
</html>
