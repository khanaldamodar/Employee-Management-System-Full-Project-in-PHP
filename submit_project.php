<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// Get the logged-in employee's email
$email = $_SESSION['email'] ?? '';

// Fetch employee ID
$sql = "SELECT id FROM employee WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    die("Error: Employee details not found.");
}

$employee = $result->fetch_assoc();
$employee_id = $employee['id'];

// Handle project submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_project'])) {
    $project_name = $_POST['submit_project'];
    $file_path = null; // Default value if no file is uploaded

    // Check if a file was uploaded
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['project_file'];

        // Validate the file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file_extension !== 'pdf') {
            echo "<script>alert('Only PDF files are allowed.'); window.location.href = 'projects.php';</script>";
            exit();
        }

        // Define the upload directory
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate a unique file name and move the file
        $file_path = $upload_dir . uniqid() . '_' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            echo "<script>alert('Failed to upload file.'); window.location.href = 'projects.php';</script>";
            exit();
        }
    }

    // Update project status and save the file path if provided
    $update_sql = "UPDATE projects 
                   SET status = 'Submitted', submission_date = NOW(), file_path = IFNULL(?, file_path) 
                   WHERE project_name = ? AND employee_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $file_path, $project_name, $employee_id);

    if ($stmt->execute()) {
        echo "<script>alert('Project submitted successfully!'); window.location.href = 'submit_project.php';</script>";
    } else {
        echo "<script>alert('Error submitting project.'); window.location.href = 'submit_project.php';</script>";
    }
}

// Pagination logic
$limit = 5; // Number of rows per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total number of projects assigned
$count_sql = "SELECT COUNT(*) AS total FROM projects WHERE employee_id = ?";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_projects = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_projects / $limit);

// Fetch projects for the current page
$sql = "SELECT * FROM projects 
        WHERE employee_id = ? 
        ORDER BY 
            CASE 
                WHEN status = 'Pending' THEN 1 
                ELSE 2 
            END,
            due_date ASC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $employee_id, $limit, $offset); 
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>
    <link rel="stylesheet" href="e_sidebar.css">
    <link rel="stylesheet" href="submit_project.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'e_sidebar.php'; ?>
    <div class="container">
        <h1>My Assigned Projects</h1>
        <table>
            <thead>
                <tr>
                    <th>S.N</th>
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
    $sn = $offset + 1; // Serial number starting from the correct page
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $sn++ . "</td>"; // Display serial number
        echo "<td>" . htmlspecialchars($row['project_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['submission_date'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['mark'] ?? 'N/A') . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        
        // Only allow project submission if status is 'Pending'
        if ($row['status'] === 'Pending') {
            echo "
            <td style='text-align: center;'>
                <form method='POST' enctype='multipart/form-data' style='display: flex; flex-direction: column; align-items: center;'>
                    <input type='hidden' name='submit_project' value='" . htmlspecialchars($row['project_name']) . "'>
                    <input type='file' name='project_file' style='margin-bottom: 5px; text-align: center;'>
                    <button type='submit' style='width: 100px;'>Submit</button>
                </form>
            </td>";
        } else {
            echo "<td style='text-align: center;'>Already Submitted / Reviewed</td>";
        }
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No projects assigned yet.</td></tr>";
}

$stmt->close();
?>
</tbody>

        </table>

        <!-- Pagination Controls -->
        <div class="pagination">       
            <a href="?page=<?php echo max($page - 1, 1); ?>" class="prev">Previous</a>
            <span>Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            <a href="?page=<?php echo min($page + 1, $total_pages); ?>" class="next">Next</a>
        </div>
    </div>
</body>
</html>
