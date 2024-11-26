<?php
// Get the database connection details from the JAWSDB_URL environment variable
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the user_email from the GET parameter
$user_email = isset($_GET['user_email']) ? $_GET['user_email'] : '';

// Ensure the email parameter is provided
if (empty($user_email)) {
    echo "Error: user_email parameter is missing";
    exit;
}

// Query to count the number of appointments for the given user_email
$query = "SELECT COUNT(*) as appointment_count FROM Bookings WHERE umak_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email); // Bind the email parameter
$stmt->execute();
$stmt->bind_result($appointment_count);
$stmt->fetch();

// Close the statement and connection
$stmt->close();
$conn->close();

// Return the appointment count as a response
echo $appointment_count;
?>
