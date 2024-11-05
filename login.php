<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mdodb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $umak_email = $_POST['umak_email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM Users WHERE umak_email = ?");
    $stmt->bind_param("s", $umak_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            echo "Login successful!";
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
}

$conn->close();
?>
