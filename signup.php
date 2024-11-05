<?php
$servername = "localhost";
$username = "root"; // default username
$password = ""; // default password is empty
$dbname = "mdodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $umak_email = $_POST['umak_email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $conn->prepare("INSERT INTO Users (student_id, umak_email, first_name, last_name, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $student_id, $umak_email, $first_name, $last_name, $password);

    if ($stmt->execute()) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
