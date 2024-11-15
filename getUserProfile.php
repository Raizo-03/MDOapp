<?php
// Include database connection file
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password is empty
$dbname = "MDOdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the email parameter is set in the GET request
if (isset($_GET['email'])) {
    $email = $_GET['email']; // Retrieve the email from the URL

    // Sanitize the email to prevent SQL injection
    $email = $conn->real_escape_string($email);

    // Fetch user details from the Users table
    $sql = "SELECT user_id, student_id, umak_email, first_name, last_name
            FROM Users  
            WHERE umak_email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        $user = $result->fetch_assoc();
        echo json_encode($user); // Return user details as JSON
    } else {
        // If no user found, return a message
        echo json_encode(['message' => 'User not found']);
    }
} else {
    // If the email parameter is missing, return an error message
    echo json_encode(['message' => 'Email parameter is missing']);
}

$conn->close();
?>
