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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>

        .logo {
            display: flex;
            align-items: center; /* Align items vertically in the center */
            justify-content: space-between; /* Space items evenly across the section */
            background-color: #0A1128;
            color: white;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .title {
            flex-grow: 1; /* Push the "Dashboard" to the center */
            text-align: center; /* Ensure the text is centered within its area */
            font-size: 18px;
            font-weight: bold;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .dashboard-title {
        flex-grow: 1; /* Push title to the center */
        text-align: center; /* Center the text within this section */
        font-size: 24px;
        font-weight: bold;
        color: white;
        }
        .header {
            background-color: #0A1128;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }
        .header .logo {
            display: flex;
            align-items: center;
        }
        .header .logo img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
        }
        .header .nav-icons {
            display: flex;
            gap: 15px;
        }
        .header .text-container {
            margin-left: 10px;
        }
        .header .nav-icons a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #1E3A8A;
            transition: background-color 0.3s;
        }
        .header .nav-icons a:hover {
            background-color: #314e9c;
        }
        .header .nav-icons img {
            width: 20px;
            height: 20px;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Logo Section -->
        <div class="logo">
            <img src="../MDO/umaklogo.png" alt="Logo">
            <div class="text-container">
                <span class="welcome">Welcome, <?php echo $_SESSION['admin_username']; ?>!</span>
            </div>
        </div>

        <div class="dashboard-title">
            <span>Dashboard</span>
        </div>

        <!-- Navigation Icons -->
        <div class="nav-icons">
            <!-- User Management -->
            <a href="user_management.php" title="User Management">
                <img src="../MDO/twopeople.png" alt="User Management">
            </a>
            <!-- Appointment Management -->
            <a href="appointment_management.php" title="Appointment Management">
                <img src="../MDO/user_journal.png" alt="Appointment Management">
            </a>
            <!-- Content Manager -->
            <a href="content_manager.php" title="Content Manager">
                <img src="../MDO/edit_white.png" alt="Content Manager">
            </a>
            <!-- Admin Profile -->
            <a href="admin_profile.php" title="Admin Profile">
                <img src="../MDO/profile.png" alt="Admin Profile">
            </a>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content">
      
        <p>This is the admin dashboard.</p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
