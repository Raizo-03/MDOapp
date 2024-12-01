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


if (isset($_GET['user_email'])) {
    $user_email = $_GET['user_email'];
    $admin_email = 'admin2@example.com';  // Admin email

    // Get all messages where:
    // 1. sender_email = logged-in user, receiver_email = admin
    // 2. sender_email = admin, receiver_email = logged-in user
    $query = "SELECT sender_email, receiver_email, message, timestamp 
              FROM Messages 
              WHERE (sender_email = ? AND receiver_email = ?) 
              OR (sender_email = ? AND receiver_email = ?)
              ORDER BY timestamp ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $admin_email, $user_email, $user_email, $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the messages and return as JSON
    $messages = array();
    while ($row = $result->fetch_assoc()) {
        $messages[] = array(
            'message' => $row['message'],
            'sender_email' => $row['sender_email'],
            'receiver_email' => $row['receiver_email'],
            'timestamp' => $row['timestamp']
        );
    }

    // Return the JSON response
    echo json_encode($messages);
}
?>
