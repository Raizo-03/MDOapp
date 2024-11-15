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

    // Check if student ID is already registered
    $checkIdStmt = $conn->prepare("SELECT * FROM Users WHERE student_id = ?");
    $checkIdStmt->bind_param("s", $student_id);
    $checkIdStmt->execute();
    $idResult = $checkIdStmt->get_result();

    if ($idResult->num_rows > 0) {
        // Student ID already exists
        echo "Student ID already registered";
    } else {
        // Check if email is already registered
        $checkEmailStmt = $conn->prepare("SELECT * FROM Users WHERE umak_email = ?");
        $checkEmailStmt->bind_param("s", $umak_email);
        $checkEmailStmt->execute();
        $emailResult = $checkEmailStmt->get_result();

        if ($emailResult->num_rows > 0) {
            // Email already exists
            echo "Email already registered";
        } else {
            // Neither student ID nor email exists, proceed with signup
            $stmt = $conn->prepare("INSERT INTO Users (student_id, umak_email, first_name, last_name, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $student_id, $umak_email, $first_name, $last_name, $password);

            if ($stmt->execute()) {
                echo "Signup successful!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $checkEmailStmt->close();
    }

    $checkIdStmt->close();
}

$conn->close();
?>
