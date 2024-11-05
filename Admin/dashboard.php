<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>
    <p>This is the admin dashboard.</p>
    <a href="logout.php">Logout</a>
    <a href="user_management.php">View Users</a>

</body>
</html>
