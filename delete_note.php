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

// Get POST data
$note_id = $_POST['note_id'];
$umak_email = $_POST['umak_email'];

// Create the delete query
$query = "DELETE FROM Notes WHERE note_id = ? AND umak_email = ?";

$stmt = $conn->prepare($query);

// Bind the parameters to the prepared statement
$stmt->bind_param("ii", $note_id, $umak_email);

// Execute the query
if ($stmt->execute()) {
    echo "Note deleted successfully!";
} else {
    echo "Error deleting note: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
