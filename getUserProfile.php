<?php
// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Create connection to JawsDB
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the email parameter is set in the GET request
if (isset($_GET['email'])) {
    $email = $_GET['email']; // Retrieve the email from the URL

    // Sanitize the email to prevent SQL injection
    $email = $conn->real_escape_string($email);

    // Fetch user details from the Users table
    $sql = "SELECT user_id, student_id, umak_email, first_name, last_name
            FROM Users  
            WHERE umak_email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        $user = $result->fetch_assoc();
        echo json_encode($user); // Return user details as JSON
    } else {
        // If no user found, return a message
        echo json_encode(['message' => 'User not found']);
    }
} else {
    // If the email parameter is missing, return an error message
    echo json_encode(['message' => 'Email parameter is missing']);
}

$conn->close();
?>
