<?php
// Get Heroku JawsDB connection information
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Create connection to JawsDB
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Test connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
} else {
    // Optional: Add this block temporarily for debugging.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "Database connection successful!";
        exit; // Prevent further execution for test
    }
}

// The rest of your PHP code
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
