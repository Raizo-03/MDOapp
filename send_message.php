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
    $timestamp = intval($_POST['timestamp']);  // Convert to integer to ensure it's a valid timestamp
    
    // Convert Unix timestamp to MySQL DATETIME format
    $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);

    // Then insert into the database
    $query = "INSERT INTO Messages (sender_email, receiver_email, message, timestamp) 
              VALUES ('$sender_email', '$receiver_email', '$message_text', '$formattedTimestamp')";

    if (mysqli_query($conn, $query)) {
        echo "Message sent successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
