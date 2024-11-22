<?php
require dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer's autoloader for PHPMailer
require '../db.php'; // Include your DB connection script
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $user_id = $data['user_id'];
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $password = !empty($data['password']) ? $data['password'] : null; // Don't hash the password
    $student_id = $data['student_id'];
    $contact_number = $data['contact_number']; // Can be null
    $address = $data['address']; // Can be null
    $guardian_contact_number = $data['guardian_contact_number']; // Can be null
    $guardian_address = $data['guardian_address']; // Can be null
    $profile_image = $data['profile_image']; // Can be null

    // Check if required fields are empty (first_name, last_name, student_id)
    if (empty($first_name) || empty($last_name) || empty($student_id)) {
        echo json_encode(['success' => false, 'error' => 'First name, last name, or student ID is missing.']);
        exit; // Stop further execution if any required field is missing
    }

    // Fetch the umak_email from the Users table based on user_id
    $emailQuery = "SELECT umak_email FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($emailQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($umak_email);
    $stmt->fetch();
    $stmt->close();

    if (empty($umak_email)) {
        echo json_encode(['success' => false, 'error' => 'Email not found for the provided user_id']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // If password is not provided, do not update it
        if (empty($data['password'])) {
            $password = null; // Do not update password if empty
        }

        // Update Users table
        $updateUserQuery = "UPDATE Users SET first_name = ?, last_name = ?, student_id = ?" . 
                           ($password !== null ? ", password = ?" : "") . " WHERE user_id = ?";
        $stmt = $conn->prepare($updateUserQuery);

        if ($password !== null) {
            $stmt->bind_param("ssssi", $first_name, $last_name, $student_id, $password, $user_id);
        } else {
            $stmt->bind_param("sssi", $first_name, $last_name, $student_id, $user_id);
        }

        if (!$stmt->execute()) {
            throw new Exception('Error updating user data: ' . $stmt->error);
        }

        // Update UserProfile table
        $updateProfileQuery = "INSERT INTO UserProfile (user_id, contact_number, address, guardian_contact_number, guardian_address, profile_image)
                               VALUES (?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE contact_number = VALUES(contact_number),
                                                       address = VALUES(address),
                                                       guardian_contact_number = VALUES(guardian_contact_number),
                                                       guardian_address = VALUES(guardian_address),
                                                       profile_image = VALUES(profile_image)";
        $stmt = $conn->prepare($updateProfileQuery);
        $stmt->bind_param("isssss", $user_id, $contact_number, $address, $guardian_contact_number, $guardian_address, $profile_image);

        if (!$stmt->execute()) {
            throw new Exception('Error updating profile data: ' . $stmt->error);
        }

        // Commit transaction
        $conn->commit();

        // Send email notification using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); 
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true; 
            $mail->Username = 'ebuscato.k12043456@umak.edu.ph'; 
            $mail->Password = 'xuke uebp cnyk qfsw'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = 587; 
            
            // Disable SSL verification (only for testing)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        
            $mail->setFrom('ebuscato.k12043456@umak.edu.ph', 'MDO App');
            $mail->addAddress($umak_email);

            $mail->Subject = 'Account Details Updated';
            $mail->Body    = "Dear $first_name $last_name,<br><br>Your account details have been successfully updated.<br><br>Regards,<br>MDOApp Admin";
            $mail->isHTML(true);

            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());  // Log the error message
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
?>
