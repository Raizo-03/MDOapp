<?php
// Include the database connection file
include('db.php'); // This includes the JawsDB connection from db.php

// Get POST data
if (isset($_POST['umak_email']) && isset($_POST['contact_number']) && isset($_POST['address']) && isset($_POST['guardian_contact_number']) && isset($_POST['guardian_address'])) {
    $umak_email = $_POST['umak_email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $guardian_contact_number = $_POST['guardian_contact_number'];
    $guardian_address = $_POST['guardian_address'];
    $operation = $_POST['operation']; // Operation could be "insert" or "update"
    
    if ($operation == "update") {
        // Update existing profile
        $query = "UPDATE UserProfile SET contact_number = ?, address = ?, guardian_contact_number = ?, guardian_address = ? WHERE umak_email = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "sssss", $contact_number, $address, $guardian_contact_number, $guardian_address, $umak_email);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update profile"]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to prepare the SQL statement"]);
        }
    } else if ($operation == "insert") {
        // Insert new profile
        $query = "INSERT INTO UserProfile (umak_email, contact_number, address, guardian_contact_number, guardian_address) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "sssss", $umak_email, $contact_number, $address, $guardian_contact_number, $guardian_address);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo json_encode(["success" => true, "message" => "Profile created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to create profile"]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to prepare the SQL statement"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid operation"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
}

// Close the database connection
mysqli_close($conn);
?>
