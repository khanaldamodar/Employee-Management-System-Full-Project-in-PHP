<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'] ?? '';

// Fetch employee details
$sql = "SELECT * FROM employee WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    die("Error: Unable to fetch employee details.");
}
$employee = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="e_sidebar.css">
    <link rel="stylesheet" href="my_profile.css">
</head>
<body>
    <?php include 'e_sidebar.php'; ?>
    <div class="container">
    <h2 style="text-align: center;">My Profile</h2>
    <img src="<?php echo htmlspecialchars($employee['pic']); ?>" alt="Profile Picture">

    <div class="profile-info">
        <div class="info"><span class="label">Full Name:</span> <?php echo htmlspecialchars($employee['firstName'] . ' ' . $employee['lastName']); ?></div>
        <div class="info"><span class="label">Email:</span> <?php echo htmlspecialchars($employee['email']); ?></div>
        <div class="info"><span class="label">Birthday:</span> <?php echo htmlspecialchars($employee['birthday']); ?></div>
        <div class="info"><span class="label">Gender:</span> <?php echo htmlspecialchars($employee['gender']); ?></div>
        <div class="info"><span class="label">Contact:</span> <?php echo htmlspecialchars($employee['contact']); ?></div>
        <div class="info"><span class="label">Address:</span> <?php echo htmlspecialchars($employee['address'] ?? 'N/A'); ?></div>
        <div class="info"><span class="label">Department:</span> <?php echo htmlspecialchars($employee['department']); ?></div>
        <div class="info"><span class="label">Degree:</span> <?php echo htmlspecialchars($employee['degree']); ?></div>
        <div class="info"><span class="label">National ID:</span> <?php echo htmlspecialchars($employee['nid']); ?></div>
        <div class="info"><span class="label">Joined On:</span> <?php echo htmlspecialchars($employee['created_at']); ?></div>
        
        <?php if (!empty($employee['cv'])): ?>
            <div class="info">
                <span class="label">CV:</span>
                <a href="<?php echo htmlspecialchars($employee['cv']); ?>" target="_blank">Download CV</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
