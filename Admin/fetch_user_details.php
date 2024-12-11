<?php
// Get Heroku JawsDB connection information
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

// Get the user_id from the URL or request
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    // Query to fetch user details
    $query = "SELECT u.user_id, u.student_id, u.umak_email, u.first_name, u.last_name, u.verified, 
              u.password,  -- Add this line to fetch the password
              p.contact_number, p.address, p.guardian_contact_number, p.guardian_address
              FROM Users u
              LEFT JOIN UserProfile p ON u.user_id = p.user_id
              WHERE u.user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User ID not provided']);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
