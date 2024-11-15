<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MDOdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $umak_email = $_POST['umak_email'];  // The email provided after password reset
    $new_password = $_POST['new_password'];  // New password from the reset link

    // Hash the new password before updating
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Prepare SQL statement to update the password
    $stmt = $conn->prepare("UPDATE Users SET password = ? WHERE umak_email = ?");
    $stmt->bind_param("ss", $hashed_password, $umak_email);

    if ($stmt->execute()) {
        echo "Password updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
