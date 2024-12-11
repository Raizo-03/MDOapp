<?php
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);



// Get the POST data
$umak_email = $_POST['umak_email']; // User's umak_email
$profile_image_base64 = $_POST['profile_image']; // Base64 encoded profile image
$operation = $_POST['operation']; // Operation to determine insert or update

// Convert Base64 to BLOB
$profile_image_blob = base64_decode($profile_image_base64);

// Step 1: Select the user_id from the Users table using umak_email
$query = "SELECT user_id FROM Users WHERE umak_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $umak_email);
$stmt->execute();
$stmt->store_result();

// Step 2: Check if the umak_email exists in the Users table
if ($stmt->num_rows > 0) {
    // Fetch the user_id from the result
    $stmt->bind_result($user_id);
    $stmt->fetch();

    // Step 3: Determine the operation (insert or update)
    if ($operation === 'insert') {
        // Insert new profile image into UserProfile table
        $insert_query = "INSERT INTO UserProfile (user_id, umak_email, profile_image) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iss", $user_id, $umak_email, $profile_image_blob);

        if ($insert_stmt->execute()) {
            echo "Profile image inserted successfully!";
        } else {
            echo "Error inserting image: " . $insert_stmt->error;
        }
        $insert_stmt->close();

    } elseif ($operation === 'update') {
        // Update existing profile image in UserProfile table
        $update_query = "UPDATE UserProfile SET profile_image = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("bi", $profile_image_blob, $user_id);

        if ($update_stmt->execute()) {
            echo "Profile image updated successfully!";
        } else {
            echo "Error updating image: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Invalid operation.";
    }

} else {
    echo "User not found with the given umak_email.";
}

// Close the statements and connection
$stmt->close();
$conn->close();
?>
