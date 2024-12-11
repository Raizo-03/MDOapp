<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_email = $_POST['sender_email'];
    $receiver_email = $_POST['receiver_email'];
    $message_text = $_POST['message_text'];
    
    // Set the timezone to Manila
    date_default_timezone_set('Asia/Manila');

    // Get the current timestamp
    $timestamp = date('Y-m-d H:i:s');

    // Prepare the query using placeholders
    $query = "INSERT INTO Messages (sender_email, receiver_email, message, timestamp) 
              VALUES (?, ?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind the parameters (the order must match the placeholders in the query)
    mysqli_stmt_bind_param($stmt, "ssss", $sender_email, $receiver_email, $message_text, $timestamp);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Message sent successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Close the connection
mysqli_close($conn);
?>
