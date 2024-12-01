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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_email = $_POST['sender_email'];  // User's email
    $receiver_email = $_POST['receiver_email'];  // Admin's email
    $message = $_POST['message_text'];  // Message content

    // Update SQL query to match the column name 'message'
    $sql = "INSERT INTO Messages (sender_email, receiver_email, message) 
            VALUES ('$sender_email', '$receiver_email', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo "Message sent successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
