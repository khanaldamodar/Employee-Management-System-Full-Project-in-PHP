<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get employee ID from URL
$employee_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch employee details
$sql = "SELECT * FROM employee WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update employee details
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];
    $nid = $_POST['nid'];
    $address = $_POST['address'];
    $department = $_POST['department'];
    $degree = $_POST['degree'];

    // Handle profile picture upload
    $picPath = $employee['pic'];
    if (!empty($_FILES['pic']['name'])) {
        $picName = time() . '_' . $_FILES['pic']['name'];
        $picPath = 'uploads/' . $picName;
        move_uploaded_file($_FILES['pic']['tmp_name'], $picPath);
    }

    // Handle CV upload
    $cvPath = $employee['cv'];
    if (!empty($_FILES['cv']['name'])) {
        $cvName = time() . '_' . $_FILES['cv']['name'];
        $cvPath = 'uploads/' . $cvName;
        move_uploaded_file($_FILES['cv']['tmp_name'], $cvPath);
    }

    $update_sql = "UPDATE employee SET 
        firstName = ?, lastName = ?, email = ?, birthday = ?, gender = ?, contact = ?, 
        nid = ?, address = ?, department = ?, degree = ?, pic = ?, cv = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssssssi", $firstName, $lastName, $email, $birthday, $gender, $contact, 
                      $nid, $address, $department, $degree, $picPath, $cvPath, $employee_id);

    if ($stmt->execute()) {
        echo "<script>alert('Employee details updated successfully!'); window.location.href='employee_detail.php?id=$employee_id';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="edit_employee.css">
    <link rel="stylesheet" href="sidebar.css">
    <script src="employee.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="form-container">
    <h2>Edit Employee</h2>

    <!-- Display Profile Picture -->
    <div class="profile-pic-container">
        <?php if (!empty($employee['pic'])): ?>
            <img src="<?php echo $employee['pic']; ?>" alt="Profile Picture" class="profile-pic">
        <?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-user"></i> First Name</label>
                <input type="text" name="firstName" value="<?php echo $employee['firstName']; ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-user"></i> Last Name</label>
                <input type="text" name="lastName" value="<?php echo $employee['lastName']; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" value="<?php echo $employee['email']; ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-graduation-cap"></i> Degree</label>
                <input type="text" name="degree" value="<?php echo $employee['degree']; ?>" required>
            </div>
           
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-birthday-cake"></i> Birthday</label>
                <input type="date" name="birthday" value="<?php echo $employee['birthday']; ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Gender</label>
                <select name="gender">
                    <option value="Male" <?php if ($employee['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($employee['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($employee['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Contact</label>
                <input type="text" name="contact" value="<?php echo $employee['contact']; ?>" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> NID</label>
                <input type="text" name="nid" value="<?php echo $employee['nid']; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Address</label>
                <input type="text" name="address" value="<?php echo $employee['address']; ?>">
            </div>
            <div class="form-group">
                <label><i class="fas fa-building"></i> Department</label>
                <input type="text" name="department" value="<?php echo $employee['department']; ?>" required>
            </div>
            
        </div>
        
        

        <div class="form-group">
            <label><i class="fas fa-image"></i> Upload Profile Picture</label>
            <input type="file" name="pic">
        </div>

        <div class="form-group">
            <label><i class="fas fa-file"></i> Upload CV</label>
            <input type="file" name="cv">
            <?php if (!empty($employee['cv'])): ?>
                <p>Current CV: <a href="<?php echo $employee['cv']; ?>" target="_blank">View CV</a></p>
            <?php endif; ?>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Changes</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='employee_detail.php?id=<?php echo $employee['id']; ?>'">
            <i class="fas fa-times-circle"></i> Cancel
            </button>
        </div>

    </form>
</div>

</body>
</html>
