<?php
header("Content-Type: application/json");

$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['user_email'])) {
    $user_email = $_GET['user_email'];

    $query = $conn->prepare("
        SELECT 
            booking_id, 
            service_type, 
            booking_date, 
            booking_time, 
            remarks, 
            status 
        FROM Bookings 
        WHERE umak_email = ? AND status = 'Completed'
    ");
    $query->bind_param("s", $user_email);
    $query->execute();
    $result = $query->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row; // Add the entire row to the bookings array
    }

    echo json_encode($bookings);
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$conn->close();
?>