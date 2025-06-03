<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include Composer's autoloader

// Database connection
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$umak_email = $_POST['umak_email']; // User's email passed from the mobile app
$service = $_POST['service'];
$service_type = $_POST['service_type'];
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
$remarks = isset($_POST['remarks']) && $_POST['remarks'] !== "null" ? $_POST['remarks'] : NULL; // Handle remarks as null
$status = "Approved"; // Set the status to "Approved"

// Validate mandatory fields
if (empty($umak_email) || empty($service) || empty($service_type) || empty($booking_date) || empty($booking_time)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    $conn->close();
    exit();
}

// Check if the user exists in the Users table
$sql = "SELECT umak_email FROM Users WHERE umak_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $umak_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "User with the provided email does not exist"]);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close(); // Close the prepared statement for user existence check

// Prepare SQL statement for inserting the booking with status
$sql = "INSERT INTO Bookings (umak_email, service, service_type, booking_date, booking_time, remarks, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $umak_email, $service, $service_type, $booking_date, $booking_time, $remarks, $status);

if ($stmt->execute()) {
    // Email sending section
    $mail = new PHPMailer(true); // Create a new PHPMailer instance

    try {
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true; 
        $mail->Username = 'umakmdo@gmail.com'; 
        $mail->Password = 'jhdp unfj togy qbxf'; 
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
    
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation - APPROVED';
        $mail->Body = "Dear User,<br><br>Your booking for <strong>$service</strong> has been successfully confirmed and <strong>APPROVED</strong>.<br>
                       Service Type: $service_type<br>
                       Booking Date: $booking_date<br>
                       Booking Time: $booking_time<br>
                       Status: <strong>Approved</strong><br><br>Remarks: " . ($remarks ? $remarks : 'N/A') . 
                       "<br><br>Thank you for using our service.";
    
        $mail->send();
        echo json_encode(["status" => "success", "message" => "Booking approved and email sent successfully"]);
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        echo json_encode(["status" => "error", "message" => "Booking inserted with approved status, but email could not be sent."]);
    }

} else {
    error_log("Booking insertion failed: " . $stmt->error);
    echo json_encode(["status" => "error", "message" => "Error inserting booking into the database."]);
}

$stmt->close();
$conn->close();

?>