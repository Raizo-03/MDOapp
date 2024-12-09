<?php


// Check availability for a specific time slot on a given date
header('Content-Type: application/json');
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed"]));
}

// Read the raw POST data
$input = file_get_contents("php://input");
error_log("Raw input: " . $input); // Log the raw input

// Decode the JSON input
$input_data = json_decode($input, true);

// Check for JSON decoding errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON format",
        "error_details" => json_last_error_msg() // Show detailed error
    ]);
    exit();
}

// Extract booking_date and booking_time from the decoded JSON
$booking_date = $input_data['booking_date'] ?? null;
$booking_time = $input_data['booking_time'] ?? null;

if (empty($booking_date) || empty($booking_time)) {
    echo json_encode(["status" => "error", "message" => "Date and time are required"]);
    exit();
}
error_log("Booking Date: $booking_date | Booking Time: $booking_time");
// Query to count how many approved bookings are there for the specific date and time
$sql = "SELECT COUNT(*) as approved_count FROM Bookings WHERE booking_date = ? AND booking_time = ? AND status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $booking_date, $booking_time);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();

// Check if there are 5 approved bookings
if ($row['approved_count'] >= 5) {
    error_log("Availability Check: Date: $booking_date | Time Slot: $booking_time | Status: unavailable");
    echo json_encode(["status" => "unavailable", "message" => "Time slot is fully booked"]);
} else {
    error_log("Availability Check: Date: $booking_date | Time Slot: $booking_time | Status: available");
    echo json_encode(["status" => "available", "message" => "Time slot is available"]);
}

$conn->close();
?>