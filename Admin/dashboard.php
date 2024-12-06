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

// Queries and counts
$activeUsers = 0;
$sqlActiveCount = "SELECT COUNT(*) as count FROM Users WHERE status = 'active'";
if ($resultActiveCount = $conn->query($sqlActiveCount)) {
    $activeUsers = $resultActiveCount->fetch_assoc()['count'] ?? 0;
}

$inactiveUsers = 0;
$sqlInactiveCount = "SELECT COUNT(*) as count FROM Users WHERE status = 'inactive'";
if ($resultInactiveCount = $conn->query($sqlInactiveCount)) {
    $inactiveUsers = $resultInactiveCount->fetch_assoc()['count'] ?? 0;
}

$pendingRequests = 0;
$SqlPendingRequests = "SELECT COUNT(*) as count FROM Bookings WHERE status = 'Pending'";
if ($resultPendingRequests = $conn->query($SqlPendingRequests)) {
    $pendingRequests = $resultPendingRequests->fetch_assoc()['count'] ?? 0;
}

$confirmedRequests = 0;
$SqlConfirmedRequests = "SELECT COUNT(*) as count FROM Bookings WHERE status = 'Approved'";
if ($resultConfirmedRequests = $conn->query($SqlConfirmedRequests)) {
    $confirmedRequests = $resultConfirmedRequests->fetch_assoc()['count'] ?? 0;
}

$unreadMessages = 0;
$SqlUnreadMessages = "SELECT COUNT(*) as count FROM Messages WHERE status = 'unread' AND receiver_email = 'admin2@example.com'";
if ($resultUnreadMessages = $conn->query($SqlUnreadMessages)) {
    $unreadMessages = $resultUnreadMessages->fetch_assoc()['count'] ?? 0;
}
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
            height: 280px;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 18px;
            border-radius: 8px;
            margin-top:10px
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <div id ="year-container">
                <label for="yearSelect">Select Year:</label>
                <select id="yearSelect">
                    <option value="all">All Years</option> <!-- Default "All Years" option -->
                </select>
            </div>
            <div class="chart-placeholder">
                <canvas id="completedChart"></canvas>
            </div>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const yearSelect = document.getElementById("yearSelect");
        const ctx = document.getElementById("completedChart").getContext("2d");
        let chart; // Store the Chart.js instance
        // Fetch data from the backend
        fetch('fetch_chart_data.php?chart_data=true')
            .then(response => response.json())
            .then(data => {
                // Group data by year, month, and service type
                const groupedData = {};
                data.forEach(item => {
                    const { year, month, service_type, total } = item;
                    if (!groupedData[service_type]) {
                        groupedData[service_type] = {};
                    }
                    if (!groupedData[service_type][year]) {
                        groupedData[service_type][year] = Array(12).fill(0);
                    }
                    // Ensure we add up all values for the same year/month/service_type combination
                    groupedData[service_type][year][month - 1] += total;
                });
                // Populate the year dropdown
                const years = [...new Set(data.map(item => item.year))];
                years.forEach(year => {
                    const option = document.createElement("option");
                    option.value = year;
                    option.textContent = year;
                    yearSelect.appendChild(option);
                });
                // Initial chart render for "All Years"
                renderChart(groupedData, "all");
                // Handle year selection
                yearSelect.addEventListener("change", (e) => {
                    renderChart(groupedData, e.target.value);
                });
            })
            .catch(error => console.error("Error fetching chart data:", error));
            function renderChart(groupedData, selectedYear) {
            const months = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            const datasets = [];
            // Create a dataset for each service type
            Object.keys(groupedData).forEach(serviceType => {
                let values = selectedYear === "all"
                    ? Array(12).fill(0).map((_, i) => {
                        return Object.keys(groupedData[serviceType]).reduce((sum, year) => {
                            return sum + (groupedData[serviceType][year][i] || 0);
                        }, 0);
                    })
                    : (groupedData[serviceType][selectedYear] || Array(12).fill(0));
                // Filter values to include only whole numbers (1, 2, 3, ...) but retain continuity for the line
                values = values.map(value => (Number.isInteger(value) && value > 0 ? value : 0));
                datasets.push({
                    label: serviceType,
                    data: values,
                    borderColor: getRandomColor(),
                    backgroundColor: "rgba(0, 0, 0, 0)", // Transparent background for lines
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: "rgba(75, 192, 192, 1)"
                });
            });
            // Destroy existing chart instance if it exists
            if (chart) chart.destroy();
            // Create a new line chart
            chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: months,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`; // Ensure whole numbers
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1, // Ensure y-axis increments by 1
                                callback: function(value) {
                                    return value; // Show all whole numbers (1, 2, 3, ...)
                                }
                            }
                        }
                    }
                }
            });
        }
        // Utility function to generate random colors for the lines
        function getRandomColor() {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 1)`;
        }
    });
</script>
</html>