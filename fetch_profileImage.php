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

$umak_email = $_GET['umak_email'] ?? '';

if (empty($umak_email)) {
    die("Email is required.");
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

        // Now use the user_id to get the profile image from UserProfile
        $query = "SELECT profile_image FROM UserProfile WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($profile_image_blob);
                $stmt->fetch();

                // Output the image as binary
                header("Content-Type: image/jpeg"); // Change to image/png if your image is PNG
                echo $profile_image_blob;
            } else {
                echo "No image found for this user.";
            }
        } else {
            echo "Error fetching profile image: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "No user found with this email.";
    }

    $stmt_user->close();
} else {
    echo "Error fetching user ID: " . $stmt_user->error;
}

$conn->close();
?>
