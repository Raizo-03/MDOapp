<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MDOdb";  // Make sure the database name is correct
$user_username = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $umak_email = $_POST['umak_email'];
    $input_password = $_POST['password'];  // Renamed the variable to avoid conflict

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM Users WHERE umak_email = ?");
    $stmt->bind_param("s", $umak_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Bind the result to a variable
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verify the password using password_verify function
        if (password_verify($input_password, $hashed_password)) {
            // Password is correct, login successful
            echo "Login successful!";
            // You may want to start a session or redirect to another page here
        } else {
            // Invalid password
            echo "Invalid credentials!";
        }
    } else {
        // User not found
        echo "User not found!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
