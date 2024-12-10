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
    
    // Step 1: Get the user_id from the Users table based on umak_email
    $query_user = "SELECT user_id FROM Users WHERE umak_email = ?";
    
    if ($stmt_user = mysqli_prepare($conn, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $umak_email);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $user_id);
        
        if (mysqli_stmt_fetch($stmt_user)) {
            // If user_id is found
            mysqli_stmt_close($stmt_user);
            
            // Step 2: Handle Insert or Update based on the operation
            if ($operation == "update") {
                // Update existing profile using user_id
                $query = "UPDATE UserProfile SET contact_number = ?, address = ?, guardian_contact_number = ?, guardian_address = ? WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "ssssi", $contact_number, $address, $guardian_contact_number, $guardian_address, $user_id);
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
                // Insert new profile using user_id
                $query = "INSERT INTO UserProfile (user_id, contact_number, address, guardian_contact_number, guardian_address) VALUES (?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "issss", $user_id, $contact_number, $address, $guardian_contact_number, $guardian_address);
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
            // If user does not exist in Users table
            echo json_encode(["success" => false, "message" => "User with this email does not exist"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare the Users SQL statement"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
}

// Close the database connection
mysqli_close($conn);
?>
