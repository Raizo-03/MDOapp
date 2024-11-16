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

// The rest of your PHP code for password update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $umak_email = $_POST['umak_email'];  // The email provided after password reset
    $new_password = $_POST['new_password'];  // New password from the reset link

    // Hash the new password before updating
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Prepare SQL statement to update the password
    $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE umak_email = ?");
    $stmt->bind_param("ss", $hashed_password, $umak_email);

    if ($stmt->execute()) {
        echo "Password updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
