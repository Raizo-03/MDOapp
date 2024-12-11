<?php
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1);

$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$umak_email = $_POST['umak_email'] ?? ''; // Validate POST data
$profile_image_base64 = $_POST['profile_image'] ?? '';
$operation = $_POST['operation'] ?? '';

if (empty($umak_email) || empty($profile_image_base64) || empty($operation)) {
    die("Invalid input. All fields are required.");
}

if (!in_array($operation, ['insert', 'update'])) {
    die("Invalid operation.");
}

$profile_image_blob = base64_decode($profile_image_base64, true);

if ($profile_image_blob === false) {
    die("Invalid Base64 data.");
}

$query = "SELECT user_id FROM Users WHERE umak_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $umak_email);

if ($stmt->execute()) {
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        if ($operation === 'insert') {
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
            $update_query = "UPDATE UserProfile SET profile_image = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $profile_image_blob, $user_id);

            if ($update_stmt->execute()) {
                echo "Profile image updated successfully!";
            } else {
                echo "Error updating image: " . $update_stmt->error;
            }
            $update_stmt->close();
        }

    } else {
        echo "User not found with the given umak_email.";
    }
} else {
    echo "Query error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
