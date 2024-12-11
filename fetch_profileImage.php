<?php
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1);

$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if (!$conn) {
    die(json_encode(["error" => "Connection failed: " . mysqli_connect_error()]));
}

$umak_email = $_GET['umak_email'] ?? '';

if (empty($umak_email)) {
    die(json_encode(["error" => "Email is required."]));
}

// Fetch user_id for the given umak_email
$query_user = "SELECT user_id FROM Users WHERE umak_email = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("s", $umak_email);

if ($stmt_user->execute()) {
    $stmt_user->store_result();

    if ($stmt_user->num_rows > 0) {
        $stmt_user->bind_result($user_id);
        $stmt_user->fetch();

        // Fetch the profile image
        $query = "SELECT profile_image FROM UserProfile WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($profile_image_blob);
                $stmt->fetch();

                // Return the image as a Base64-encoded string in JSON
                $base64_image = base64_encode($profile_image_blob);
                echo json_encode(["image" => $base64_image]);
            } else {
                echo json_encode(["error" => "No image found for this user."]);
            }
        } else {
            echo json_encode(["error" => "Error fetching profile image: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "No user found with this email."]);
    }
    $stmt_user->close();
} else {
    echo json_encode(["error" => "Error fetching user ID: " . $stmt_user->error]);
}

$conn->close();
?>
