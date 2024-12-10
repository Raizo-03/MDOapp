<?php

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

// Get the date parameter from the request (e.g., '2024-12-27')
if (isset($_GET['date']) && isset($_GET['email'])) {
    $selectedDate = $_GET['date']; // Format: YYYY-MM-DD
    $userEmail = $_GET['email'];

    // Query to check if there's any booking on the selected date for the given user
    $sql = "SELECT * FROM Bookings WHERE booking_date = ? AND umak_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $selectedDate, $userEmail); // Bind the date and email parameters

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are any bookings for the selected date
    if ($result->num_rows > 0) {
        // Booking found, return true
        echo json_encode(array("booking_found" => true));
    } else {
        // No booking found, return false
        echo json_encode(array("booking_found" => false));
    }

    $stmt->close();
} else {
    echo json_encode(array("message" => "Missing parameters"));
}

$conn->close();
?>
