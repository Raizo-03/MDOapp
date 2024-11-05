<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = ""; // XAMPP default password
$dbname = "MDOdb";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select all users
$sql = "SELECT user_id, student_id, umak_email, first_name, last_name, created_at FROM Users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
</head>
<body>
    <h2>User List</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Student ID</th>
            <th>UMAK Email</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Created At</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["user_id"] . "</td>";
                echo "<td>" . $row["student_id"] . "</td>";
                echo "<td>" . $row["umak_email"] . "</td>";
                echo "<td>" . $row["first_name"] . "</td>";
                echo "<td>" . $row["last_name"] . "</td>";
                echo "<td>" . $row["created_at"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No users found.</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$conn->close();
?>
