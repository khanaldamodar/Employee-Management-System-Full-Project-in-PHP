<?php
session_start();
include 'db_connection.php'; // Ensure this file contains the correct database connection setup

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $nid = mysqli_real_escape_string($conn, $_POST['nid']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $degree = mysqli_real_escape_string($conn, $_POST['degree']);
    $created_at = date('Y-m-d H:i:s');

    // File upload handling
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $pic = $_FILES['image']['name'];
    $cv = $_FILES['cv']['name'];
    $pic_tmp = $_FILES['image']['tmp_name'];
    $cv_tmp = $_FILES['cv']['tmp_name'];

    $pic_path = $target_dir . time() . "_" . basename($pic);
    $cv_path = $target_dir . time() . "_" . basename($cv);

    if (move_uploaded_file($pic_tmp, $pic_path) && move_uploaded_file($cv_tmp, $cv_path)) {
        // Insert into database
        $sql = "INSERT INTO employee (firstName, lastName, email, password, birthday, gender, contact, nid, address, department, degree, pic, cv, created_at) 
                VALUES ('$firstName', '$lastName', '$email', '$password', '$birthday', '$gender', '$contact', '$nid', '$address', '$department', '$degree', '$pic_path', '$cv_path', '$created_at')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Employee added successfully!'); window.location.href='view_employee.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('File upload failed.'); window.history.back();</script>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="add_employee.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="employee.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="form-container">
    <h2>Add Employee</h2>
    <form action="add_employee.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm();">
    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-user"></i> First Name</label>
            <input type="text" name="firstName" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-user"></i> Last Name</label>
            <input type="text" name="lastName" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-lock"></i> Password</label>
            <input type="password" id="password" name="password" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-birthday-cake"></i> Birthday</label>
            <input type="date" id="birthday" name="birthday" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-venus-mars"></i> Gender</label>
            <select name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-phone"></i> Contact</label>
            <input type="tel" id="contact" name="contact" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-id-card"></i> National ID</label>
            <input type="text" id="nid" name="nid" required placeholder="40013301321">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Address</label>
            <input type="text" name="address" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-building"></i> Department</label>
            <input type="text" name="department" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><i class="fas fa-graduation-cap"></i> Degree</label>
            <input type="text" name="degree" required>
        </div>
        <div class="form-group">
            <label><i class="fas fa-file"></i> Upload CV</label>
            <input type="file" id="cv" name="cv" required>
        </div>
    </div>

    <div class="form-group">
        <label><i class="fas fa-image"></i> Upload Image</label>
        <input type="file" id="image" name="image" required>
    </div>

    <button type="submit"><i class="fas fa-plus-circle"></i> Submit</button>
</form>
</div>

</body>
</html>