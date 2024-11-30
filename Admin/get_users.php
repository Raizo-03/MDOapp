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

// Using prepared statements to prevent SQL injection
$query =  "SELECT DISTINCT u.umak_email, u.first_name, u.last_name 
           FROM Messages m
           JOIN Users u ON m.sender_email = u.umak_email
           WHERE m.receiver_email = 'admin2@example.com'";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute();

// Get the result
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Output the users as JSON
echo json_encode($users);
?>
