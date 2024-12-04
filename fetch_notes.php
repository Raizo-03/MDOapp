<?php
header('Content-Type: application/json');

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

// Check if the email is provided in the POST request
if (isset($_POST['umak_email']) && !empty($_POST['umak_email'])) {
    $umak_email = $conn->real_escape_string($_POST['umak_email']);

    // Query to fetch notes related to the user
    $sql = "SELECT note_id, title, symptoms, mood, medicine, created_at 
            FROM Notes 
            WHERE umak_email = '$umak_email' 
            ORDER BY created_at DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $notes = [];

        // Fetch all rows
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }

        echo json_encode(["success" => true, "notes" => $notes]);
    } else {
        echo json_encode(["success" => true, "notes" => [], "message" => "No notes found."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid or missing email address."]);
}

// Close the connection
$conn->close();
?>
