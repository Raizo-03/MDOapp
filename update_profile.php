<?php
// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1);

// Create connection to JawsDB
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the email is provided
if (isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $updates = [];

    // Dynamically build the update query based on provided fields
    if (isset($_POST['first_name'])) {
        $first_name = $conn->real_escape_string($_POST['first_name']);
        $updates[] = "first_name = '$first_name'";
    }
    if (isset($_POST['last_name'])) {
        $last_name = $conn->real_escape_string($_POST['last_name']);
        $updates[] = "last_name = '$last_name'";
    }
    if (isset($_POST['password'])) {
        $password = $conn->real_escape_string($_POST['password']);
        $updates[] = "password = '$password'";
    }

    if (!empty($updates)) {
        $update_query = "UPDATE Users SET " . implode(", ", $updates) . " WHERE umak_email = '$email'";
        
        if ($conn->query($update_query) === TRUE) {
            echo json_encode(['message' => 'User details updated successfully']);
        } else {
            echo json_encode(['message' => 'Error updating user details: ' . $conn->error]);
        }
    } else {
        echo json_encode(['message' => 'No fields provided for update']);
    }
} else {
    echo json_encode(['message' => 'Email parameter is missing']);
}

$conn->close();
?>
