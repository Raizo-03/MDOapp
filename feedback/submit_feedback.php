<?php
header("Content-Type: application/json");
// MySQL connection details
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    // Extract feedback data
    $feedback = $data['feedback'] ?? '';
    $rating = $data['rating'] ?? 0;
    $user_email = $data['user_email'] ?? '';  // User email
    $booking_id = $data['booking_id'] ?? 0;  // Booking ID
    $service_type = $data['service_type'] ?? 'General';  // Default service type if not passed
    $name = $data['user_email'];  
    // Validate inputs
    if (!empty($feedback) && $rating > 0 && !empty($user_email) && $booking_id > 0) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO Feedback (user_email, booking_id, name, service_type, rating, message) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Failed to prepare the SQL statement: " . $conn->error);
        }
        // Bind the parameters (Make sure to bind correct data types)
        $stmt->bind_param("sissis", $user_email, $booking_id, $name, $service_type, $rating, $feedback);
        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Feedback saved."]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to insert feedback: " . $stmt->error]);
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