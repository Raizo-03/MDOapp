<?php
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if (isset($_GET['umak_email'])) {
    $umak_email = $_GET['umak_email'];

    $query_user = "SELECT user_id FROM Users WHERE umak_email = ?";
    if ($stmt_user = mysqli_prepare($conn, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "s", $umak_email);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $user_id);

        if (mysqli_stmt_fetch($stmt_user)) {
            mysqli_stmt_close($stmt_user);

            $query_medical = "SELECT sex, blood_type, allergies, medical_conditions, medications FROM medical_info WHERE user_id = ?";
            if ($stmt_medical = mysqli_prepare($conn, $query_medical)) {
                mysqli_stmt_bind_param($stmt_medical, "i", $user_id);
                mysqli_stmt_execute($stmt_medical);
                mysqli_stmt_bind_result($stmt_medical, $sex, $blood_type, $allergies, $medical_conditions, $medications);

                if (mysqli_stmt_fetch($stmt_medical)) {
                    echo json_encode([
                        "exists" => true,
                        "sex" => $sex,
                        "blood_type" => $blood_type,
                        "allergies" => $allergies,
                        "medical_conditions" => $medical_conditions,
                        "medications" => $medications
                    ]);
                } else {
                    echo json_encode(["exists" => false, "message" => "No medical record found."]);
                }
                mysqli_stmt_close($stmt_medical);
            } else {
                echo json_encode(["error" => "Failed to prepare medical info SQL."]);
            }
        } else {
            echo json_encode(["exists" => false, "message" => "User not found."]);
        }
    } else {
        echo json_encode(["error" => "Failed to prepare user lookup SQL."]);
    }
} else {
    echo json_encode(["error" => "umak_email not provided"]);
}
mysqli_close($conn);
?>
