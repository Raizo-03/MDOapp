<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);


// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Check if required parameters are sent
if (isset($_POST['umak_email']) && isset($_POST['booking_id'])) {
    $umak_email = $conn->real_escape_string($_POST['umak_email']);
    $booking_id = $conn->real_escape_string($_POST['booking_id']);

    // Prepare the DELETE query
    $query = "DELETE FROM Bookings WHERE umak_email = ? AND booking_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $umak_email, $booking_id);

    if ($stmt->execute()) {
        // Booking deleted successfully, send email
        if (sendDeletionEmail($umak_email, $booking_id)) {
            echo json_encode(["success" => true, "message" => "Booking deleted successfully. An email confirmation has been sent."]);
        } else {
            echo json_encode(["success" => true, "message" => "Booking deleted successfully, but email confirmation failed."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting booking: " . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request. Missing parameters."]);
}

$conn->close();

/**
 * Sends a booking deletion confirmation email.
 *
 * @param string $email User's email address
 * @param int $bookingID Booking ID that was deleted
 * @return bool True if email sent successfully, False otherwise
 */
function sendDeletionEmail($email, $bookingID) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ebuscato.k12043456@umak.edu.ph';
        $mail->Password = 'xuke uebp cnyk qfsw';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('ebuscato.k12043456@umak.edu.ph', 'MDO App');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Deletion Confirmation';
        $mail->Body = "
            <p>Dear User,</p>
            <p>Your booking with ID <strong>$bookingID</strong> has been successfully deleted.</p>
            <p>If you have any questions or need further assistance, please feel free to contact us.</p>
            <p>Best regards,<br>UMAK Medical and Dental Office</p>
        ";
        $mail->AltBody = "Dear User,\n\nYour booking with ID $bookingID has been successfully deleted.\n\nBest regards,\nUMAK Medical and Dental Office";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
