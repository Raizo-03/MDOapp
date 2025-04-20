<?php
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if (
    isset($_POST['umak_email']) &&
    isset($_POST['sex']) &&
    isset($_POST['blood_type']) &&
    isset($_POST['allergies']) &&
    isset($_POST['medical_conditions']) &&
    isset($_POST['medications']) &&
    isset($_POST['operation'])
) {
    $umak_email = $_POST['umak_email'];
    $sex = $_POST['sex'];
    $blood_type = $_POST['blood_type'];
    $allergies = $_POST['allergies'];
    $medical_conditions = $_POST['medical_conditions'];
    $medications = $_POST['medications'];
    $operation = $_POST['operation'];

    $query_user = "SELECT user_id FROM Users WHERE umak_email = ?";
    if ($stmt_user = mysqli_prepare($conn, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $umak_email);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $user_id);

        if (mysqli_stmt_fetch($stmt_user)) {
            mysqli_stmt_close($stmt_user);

            if ($operation == "update") {
                $query = "UPDATE medical_info SET sex = ?, blood_type = ?, allergies = ?, medical_conditions = ?, medications = ? WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "sssssi", $sex, $blood_type, $allergies, $medical_conditions, $medications, $user_id);
                    $result = mysqli_stmt_execute($stmt);
                    echo json_encode(["success" => $result, "message" => $result ? "Medical info updated." : "Failed to update."]);
                    mysqli_stmt_close($stmt);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to prepare update SQL."]);
                }
            } elseif ($operation == "insert") {
                $query = "INSERT INTO medical_info (user_id, umak_email, sex, blood_type, allergies, medical_conditions, medications) VALUES (?, ?, ?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "issssss", $user_id, $umak_email, $sex, $blood_type, $allergies, $medical_conditions, $medications);
                    $result = mysqli_stmt_execute($stmt);
                    echo json_encode(["success" => $result, "message" => $result ? "Medical info saved." : "Failed to insert."]);
                    mysqli_stmt_close($stmt);
                } else {
                    echo json_encode(["success" => false, "message" => "Failed to prepare insert SQL."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Invalid operation."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "User not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare user lookup SQL."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
}
mysqli_close($conn);
?>
