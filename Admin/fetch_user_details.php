<?php
require '../db.php'; // Include your DB connection script

$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    $query = "SELECT u.user_id, u.student_id, u.umak_email, u.first_name, u.last_name, u.verified, 
    u.password,  -- Add this line to fetch the password
    p.contact_number, p.address, p.guardian_contact_number, p.guardian_address, p.profile_image
FROM Users u
LEFT JOIN UserProfile p ON u.user_id = p.user_id
WHERE u.user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User ID not provided']);
}
?>