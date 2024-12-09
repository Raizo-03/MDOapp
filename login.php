<?php
// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Create connection to JawsDB
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Test connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    // Optional: Add this block temporarily for debugging.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Database connection successful!";
        exit; // Prevent further execution for test
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $umak_email = $_POST['umak_email'];
    $input_password = $_POST['password'];  // Renamed the variable to avoid conflict

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password, status FROM Users WHERE umak_email = ?");
    $stmt->bind_param("s", $umak_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result to variables
        $stmt->bind_result($hashed_password, $status);
        $stmt->fetch();

        // Check if the account status is "Active"
        if ($status === 'inactive') {
            echo "Your account is inactive.";
        } else {
            // Verify the password using password_verify function
            if (password_verify($input_password, $hashed_password)) {
                // Password is correct, login successful
                echo "Login successful!";
                // You may want to start a session or redirect to another page here
            } else {
                // Invalid password
                echo "Invalid credentials!";
            }
        }
    } else {
        // User not found
        echo "User not found!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
