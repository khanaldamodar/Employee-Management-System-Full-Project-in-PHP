<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee_management";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role == 'admin') {
        $sql = "SELECT * FROM admin WHERE admin_username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['user_type'] = 'admin';
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid admin credentials.";
        }
    } else if ($role == 'employee') {
        $sql = "SELECT * FROM employee WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['user_type'] = 'employee';
            $_SESSION['email'] = $username;
            header("Location: my_profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid employee credentials.";
        }
    } else {
        $_SESSION['error'] = "Invalid role selected.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin/Employee Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <h1 ><span style="color: #20a506;">Work</span><span style="color: rgb(76, 184, 184);">Sphere</span></h1>
        <nav>
            <ul>
                <li><a href="home.html">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="contact.html">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Welcome Back</h2>
        <p style="color:#ffffff; font-size: 1.1rem; margin-bottom: 20px;">Please log in to access your dashboard.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message" style="color: red; text-align: center; font-size: 0.9rem;">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Clear the error message after displaying it
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <div class="input-name">
                    <i class="fas fa-user icon"></i>
                    <input type="text" name="username" placeholder="Username or Email" class="textfield" required>
                </div>

                <div class="input-name">
                    <i class="fas fa-lock icon"></i>
                    <input type="password" name="password" placeholder="Password" class="textfield" required>
                </div>

                <div class="input-name">
                    <select name="role" id="role" class="select" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>

                <div>
                    <input type="submit" class="button" value="Login">
                </div>
            </div>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2024 FAST EMS. All rights reserved.</p>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
        </div>
    </footer>
</body>
</html>