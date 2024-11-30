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
$data = json_decode(file_get_contents("php://input"));

$sender_email = $data->sender_email;
$receiver_email = $data->receiver_email;
$message_text = $data->message_text;

// Ensure all required fields are present
if (empty($sender_email) || empty($receiver_email) || empty($message_text)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Using prepared statements to prevent SQL injection
$query = "INSERT INTO Messages (sender_email, receiver_email, message) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sss", $sender_email, $receiver_email, $message_text);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>