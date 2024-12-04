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
// Get input data from POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $umak_email = $_POST['umak_email'];
    $title = $_POST['title'];
    $symptoms = $_POST['symptoms'];
    $mood = $_POST['mood'];
    $medicine = $_POST['medicine'];
    $created_at = $_POST['created_at']; // Ensure the client provides this datetime value

    // Validate inputs
    if (empty($umak_email) || empty($title) || empty($symptoms) || empty($created_at)) {
        echo json_encode(["success" => false, "message" => "Required fields are missing."]);
        exit;
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO Notes (umak_email, title, symptoms, mood, medicine, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $umak_email, $title, $symptoms, $mood, $medicine, $created_at);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Note inserted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
