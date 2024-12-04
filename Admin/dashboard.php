<?php
session_start();

$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Count active users
$sqlActiveCount = "SELECT COUNT(*) as count FROM Users WHERE status = 'active'";
$resultActiveCount = $conn->query($sqlActiveCount);
$activeUsers = $resultActiveCount->fetch_assoc()['count'] ?? 0;

// Count inactive users
$sqlInactiveCount = "SELECT COUNT(*) as count FROM Users WHERE status = 'inactive'";
$resultInactiveCount = $conn->query($sqlInactiveCount);
$inactiveUsers = $resultInactiveCount->fetch_assoc()['count'] ?? 0;

$SqlPendingRequests = "SELECT COUNT(*) as count FROM Bookings WHERE status = 'Pending'";
$pendingRequests = $conn->query($SqlPendingRequests);

$SqlConfirmedRequests = "SELECT COUNT(*) as count FROM Bookings WHERE status = 'Approved'"; 
$confirmedRequests = $conn->query($SqlConfirmedRequests);

$SqlUnreadMessages =  "SELECT COUNT(*) as count FROM Messages WHERE status = 'unread'";
$unreadMessages =  $conn->query($SqlUnreadMessages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../MDO/mdo_logo_circle.png">
    <style>
  body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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
        .header .dashboard-title {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }
        .header .nav-icons {
            display: flex;
            gap: 15px;
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
            padding: 10px;
        }
        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            margin-top: 10px;
            text-align: center;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .chart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
            margin-bottom:30px;
        }
        .chart-placeholder {
            height: 300px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 18px;
            border-radius: 8px;
        }
        a {
            text-decoration: none;
            color: #1E3A8A;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color:white;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            background-color:red;
            float:right;
        }

        .back-link-container {
            position: fixed;
            bottom: 20px; /* Distance from the bottom */
            right: 20px; /* Distance from the right */
            z-index: 10; /* Ensures it stays on top */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Logo Section -->
        <div class="logo">
            <a href="dashboard.php" title="Dashboard">
            <img src="../MDO/umaklogo.png" alt="Logo">
            </a>
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
            <a href="appointment_management.php#requests" title="Appointment Management">
                <img src="../MDO/user_journal.png" alt="Appointment Management">
            </a>
            <!-- Content Manager -->
            <a href="content_manager.php#chat" title="Content Manager">
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
      
    <!-- Content Section -->
    <div class="content">
        <div class="grid">
            <a href="user_management.php?tab=inactive#active">
            <div class="card">
                <h3>Active Users</h3>
                <p><?php echo $activeUsers; ?></p>
            </div>
            </a>
            <a href="user_management.php?tab=inactive#inactive">
            <div class="card">
                <h3>Inactive Users</h3>
                <p><?php echo $inactiveUsers; ?></p>
            </div>
            </a>
            <a href="appointment_management.php#requests">
            <div class="card">
                <h3>Pending Requests</h3>
                <p><?php echo $pendingRequests; ?></p>
            </div>
            </a>
            <a href="appointment_management.php#confirmed">
            <div class="card">
                <h3>Confirmed Requests</h3>
                <p><?php echo $confirmedRequests; ?></p>
            
            </div>
            </a>
            <a href="content_manager.php#chat">
            <div class="card">
           
                <h3>Unread Messages</h3>
                <p><?php echo $unreadMessages; ?></p>
            </div>
            </a>
        </div>

        <div class="chart-container">
            <h3>Yearly Statistics</h3>
            <div class="chart-placeholder">[Graph Placeholder]</div>
        </div>

        <div class="grid">
            <div class="card">
                <h3>Announcements</h3>
                <p><a href="content_manager.php#announcements">Manage Announcements</a></p>
            </div>
            <div class="card">
                <h3>Trivia</h3>
                <p><a href="content_manager.php#trivia">Manage Trivia</a></p>
            </div>
        </div>
        <div class="back-link-container">
            <a href="logout.php" class="back-link">Logout</a>
        </div>
</body>
</html>