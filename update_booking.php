<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include Composer's autoloader

// Database connection using Heroku JAWSDB
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1);

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $umak_email = $_POST['umak_email'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $booking_time = $_POST['booking_time'] ?? null;
    $remarks = $_POST['remarks'] ?? null;

    if ($booking_id && $umak_email && $booking_date && $booking_time) {
        $stmt = $conn->prepare("
            UPDATE Bookings 
            SET booking_date = ?, booking_time = ?, remarks = ?
            WHERE booking_id = ? AND umak_email = ?
        ");
        $stmt->bind_param("sssis", $booking_date, $booking_time, $remarks, $booking_id, $umak_email);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            // Email sending section
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true; 
                $mail->Username = 'umakmdo@gmail.com'; 
                $mail->Password = 'jhdp unfj togy qbxf'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port = 587;

                // Disable SSL verification (for testing only)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                $mail->setFrom('ebuscato.k12043456@umak.edu.ph', 'MDO App');
                $mail->addAddress($umak_email);

                $mail->isHTML(true);
                $mail->Subject = 'Booking Updated Successfully';
                $mail->Body = "Dear User,<br><br>Your booking has been updated successfully.<br>
                               <strong>Updated Details:</strong><br>
                               Booking Date: $booking_date<br>
                               Booking Time: $booking_time<br>
                               Remarks: " . ($remarks ?: 'N/A') . "<br><br>Thank you for using our service.";

                $mail->send();
                echo json_encode(["status" => "success", "message" => "Booking updated and email sent successfully."]);
            } catch (Exception $e) {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                echo json_encode(["status" => "error", "message" => "Booking updated, but email could not be sent."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update booking. Please check the details."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Missing required parameters."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>
