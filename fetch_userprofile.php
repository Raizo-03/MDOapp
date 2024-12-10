<?php
// Include the database connection file
include('db.php'); // This includes the JawsDB connection from db.php

// Get umak_email from the GET request
if (isset($_GET['umak_email'])) {
    $umak_email = $_GET['umak_email'];

    // Query to check if umak_email exists in UserProfile and fetch additional fields
    $query = "SELECT contact_number, address, guardian_contact_number, guardian_address FROM UserProfile WHERE umak_email = ?";
    
    // Prepare and execute the query using the JawsDB connection
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the parameter and execute the statement
        mysqli_stmt_bind_param($stmt, "s", $umak_email); // "s" is for string type
        mysqli_stmt_execute($stmt);
        
        // Bind the result variables
        mysqli_stmt_bind_result($stmt, $contact_number, $address, $guardian_contact_number, $guardian_address);

        // Check if any row is returned
        if (mysqli_stmt_fetch($stmt)) {
            // If the email exists, return the data
            echo json_encode([
                'exists' => true,
                'contact_number' => $contact_number,
                'address' => $address,
                'guardian_contact_number' => $guardian_contact_number,
                'guardian_address' => $guardian_address
            ]);
        } else {
            // If the email does not exist
            echo json_encode(['exists' => false]);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['error' => 'Failed to prepare the SQL statement']);
    }
} else {
    echo json_encode(['error' => 'umak_email not provided']);
}

// Close the database connection
mysqli_close($conn);
?>
