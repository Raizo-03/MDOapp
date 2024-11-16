<?php
session_start();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get Heroku JawsDB connection information
    $jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
    $jawsdb_server = $jawsdb_url["host"];
    $jawsdb_username = $jawsdb_url["user"];
    $jawsdb_password = $jawsdb_url["pass"];
    $jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

    // Connect to the database
    $conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

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
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Admin not found.";
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>

    <h2>Admin Login</h2>

    <!-- Display error message if login fails -->
    <?php if (isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>

    <form action="admin_login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <button type="submit">Login</button>
    </form>

</body>
</html>
