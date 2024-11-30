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
// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$user_email = $data['user_email']; // Accessing the user email from JSON

// Update query to mark messages as 'read'
$sql = "UPDATE Messages SET status='read' WHERE sender_email = ? AND status = 'unread'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}

$stmt->close();
$conn->close();
?>
