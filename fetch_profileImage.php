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

$query_user = "SELECT user_id FROM Users WHERE umak_email = ?";


$query = "SELECT profile_image FROM UserProfile WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $umak_email);

if ($stmt->execute()) {
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($profile_image_blob);
        $stmt->fetch();

        // Output the image as binary
        header("Content-Type: image/jpeg"); // Default to JPEG, change to PNG if needed
        echo $profile_image_blob;
    } else {
        echo "No image found for this user.";
    }
} else {
    echo "Query error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
