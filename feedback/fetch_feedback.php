<?php
header("Content-Type: application/json");
// Connect to the database
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit();
}
// Fetch feedback
$sql = "SELECT id, user_email, booking_id, name, service, service_type, rating, message, created_at 
        FROM feedback 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
// Check if there are results
if ($result->num_rows > 0) {
    $feedback = [];
    // Fetch all the feedback records
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
    // Return the feedback data as JSON
    echo json_encode(["success" => true, "feedback" => $feedback]);
} else {
    // No feedback found
    echo json_encode(["success" => true, "feedback" => []]);
}
// Close the database connection
$conn->close();
?>