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

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Query to count unread messages for the user
    $query = "SELECT COUNT(*) AS unread_count FROM messages WHERE sender_email = ? AND status = 'unread'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Return the unread count as JSON
    echo json_encode(['unread_count' => $row['unread_count']]);
} else {
    echo json_encode(['error' => 'No email provided']);
}

$conn->close();

?>
