<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: alogin.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Set your MySQL password if applicable
$dbname = "employee_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get employee ID from URL
$employee_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the employee details
$sql = "SELECT * FROM employee WHERE id = $employee_id";
$result = $conn->query($sql);
$employee = $result->fetch_assoc();

// Fetch the total points (marks) for the employee from the projects table
$points_sql = "SELECT SUM(mark) as total_points FROM projects WHERE employee_id = $employee_id";
$points_result = $conn->query($points_sql);
$points = $points_result->fetch_assoc();
$total_points = $points['total_points'] ? $points['total_points'] : 0; // Default to 0 if no points exist
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link rel="stylesheet" href="employee_detail.css">
    <link rel="stylesheet" href="sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <h1>Employee Details</h1>

    <?php if ($employee): ?>
        <div class="employee-detail">
            <!-- Clickable Image -->
            <img src="<?php echo $employee['pic']; ?>" alt="Employee Picture" style="width: 150px; height: 150px; border-radius: 50%; cursor: pointer;" id="employeePhoto">
            <h2><?php echo $employee['firstName'] . ' ' . $employee['lastName']; ?></h2>
            <p><strong>Employee ID:</strong> <?php echo $employee['id']; ?></p>
            <p><strong>Email:</strong> <?php echo $employee['email']; ?></p>
            <p><strong>Birthday:</strong> <?php echo $employee['birthday']; ?></p>
            <p><strong>Gender:</strong> <?php echo $employee['gender']; ?></p>
            <p><strong>Contact:</strong> <?php echo $employee['contact']; ?></p>
            <p><strong>NID:</strong> <?php echo $employee['nid']; ?></p>
            <p><strong>Address:</strong> <?php echo $employee['address']; ?></p>
            <p><strong>Department:</strong> <?php echo $employee['department']; ?></p>
            <p><strong>Degree:</strong> <?php echo $employee['degree']; ?></p>

            <!-- Display Total Points -->
            <p><strong>Total Points:</strong> <?php echo $total_points; ?></p>

            <!-- View CV Section -->
            <?php if (!empty($employee['cv'])): ?>
                <a href="<?php echo $employee['cv']; ?>" target="_blank" class="view-cv-btn">View CV</a>
            <?php else: ?>
                <p><em>No CV uploaded.</em></p>
            <?php endif; ?>
            <a href="edit_employee.php?id=<?php echo $employee['id']; ?>" class="edit-btn">Edit Employee</a>

        </div>

        <!-- Modal for Full-Screen Image -->
        <div id="photoModal" class="modal">
            <span class="modal-close" id="modalClose">&times;</span>
            <img class="modal-content" id="modalImage">
        </div>
    <?php else: ?>
        <p>Employee not found.</p>
    <?php endif; ?>
</body>
<script>
    // JavaScript for Modal
    const modal = document.getElementById('photoModal');
    const img = document.getElementById('employeePhoto');
    const modalImg = document.getElementById('modalImage');
    const closeModal = document.getElementById('modalClose');

    img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = this.src;
    }

    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    // Close modal when clicking outside the image
    modal.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
</html>

<?php
$conn->close();
?>
