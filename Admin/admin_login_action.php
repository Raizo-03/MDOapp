<?php

session_start();

// Check if username and password are set in POST
if (isset($_POST['username']) && isset($_POST['password'])) {
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
            echo "Invalid username or password.";
        }
    } else {
        echo "Admin not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Username and password must be provided.";
}

?>
