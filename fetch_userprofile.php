<?php
// Include the database connection file
include('db.php'); // This includes the JawsDB connection from db.php

// Get umak_email from the GET request
if (isset($_GET['umak_email'])) {
    $umak_email = $_GET['umak_email'];

    // Step 1: Check if the umak_email exists in the Users table and get the user_id
    $query_user = "SELECT user_id FROM Users WHERE umak_email = ?";
    
    if ($stmt_user = mysqli_prepare($conn, $query_user)) {
        // Bind the parameter and execute the statement
        mysqli_stmt_bind_param($stmt_user, "s", $umak_email);
        mysqli_stmt_execute($stmt_user);

        // Bind the result variable
        mysqli_stmt_bind_result($stmt_user, $user_id);

        // Check if any row is returned (i.e., if the user exists)
        if (mysqli_stmt_fetch($stmt_user)) {
            // Step 2: If user exists, fetch the user profile using user_id
            $query_profile = "SELECT contact_number, address, guardian_contact_number, guardian_address 
                              FROM UserProfile WHERE user_id = ?";
            
            if ($stmt_profile = mysqli_prepare($conn, $query_profile)) {
                // Bind the parameter and execute the statement
                mysqli_stmt_bind_param($stmt_profile, "i", $user_id); // "i" for integer type (user_id is INT)
                mysqli_stmt_execute($stmt_profile);

                // Bind the result variables
                mysqli_stmt_bind_result($stmt_profile, $contact_number, $address, $guardian_contact_number, $guardian_address);

                // Check if any row is returned
                if (mysqli_stmt_fetch($stmt_profile)) {
                    // If the profile exists, return the data
                    echo json_encode([
                        'exists' => true,
                        'contact_number' => $contact_number,
                        'address' => $address,
                        'guardian_contact_number' => $guardian_contact_number,
                        'guardian_address' => $guardian_address
                    ]);
                } else {
                    // If the profile does not exist
                    echo json_encode(['exists' => false]);
                }

                // Close the profile statement
                mysqli_stmt_close($stmt_profile);
            } else {
                echo json_encode(['error' => 'Failed to prepare the UserProfile SQL statement']);
            }
        } else {
            // If the user does not exist in the Users table
            echo json_encode(['exists' => false, 'error' => 'User with this email does not exist']);
        }

        // Close the user statement
        mysqli_stmt_close($stmt_user);
    } else {
        echo json_encode(['error' => 'Failed to prepare the Users SQL statement']);
    }
} else {
    echo json_encode(['error' => 'umak_email not provided']);
}

// Close the database connection
mysqli_close($conn);
?>
