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
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_email = $data['user_email'] ?? '';
    if (!empty($user_email)) {
        // Query to fetch the latest booking
        $sql = "SELECT b.booking_id, b.service_type, 
                       (SELECT COUNT(*) FROM Feedback f WHERE f.booking_id = b.booking_id AND f.user_email = ?) AS has_feedback 
                FROM Bookings b 
                WHERE b.umak_email = ? 
                ORDER BY b.booking_id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_email, $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $booking = $result->fetch_assoc();
            echo json_encode([
                "success" => true,
                "booking" => [
                    "booking_id" => $booking['booking_id'],
                    "service_type" => $booking['service_type'],
                    "has_feedback" => $booking['has_feedback'] > 0 // Boolean flag
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "No bookings found."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Only POST method is allowed."]);
}
$conn->close();
?>