<?php
session_start();
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'employee') {
    session_unset();
    session_destroy();
}

if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    session_unset();
    session_destroy();
}

header("Location: login.php");
exit;
?>
