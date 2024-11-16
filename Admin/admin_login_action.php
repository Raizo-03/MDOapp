<?php
session_start();

// Get environment variables for Heroku
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username and password from POST request
$admin_username = $_POST['username'];
$admin_password = $_POST['password'];

// Prepare SQL query to find admin by username
$sql = "SELECT * FROM Admins WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

// Check if admin exists
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();

    // Directly compare the entered password with the stored password
    if ($admin_password === $admin['password']) {
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        
        // Redirect to the admin dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    echo "Admin not found.";
}

$stmt->close();
$conn->close();
?>
