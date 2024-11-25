<?php
require dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer's autoloader for PHPMailer

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['action'])) {
    $id = $data['id'];
    $action = $data['action'];

    // Fetch booking details and user information by joining with the Users table
    $query = "
        SELECT b.*, u.first_name, u.last_name, u.umak_email
        FROM bookings b
        JOIN users u ON b.umak_email = u.umak_email
        WHERE b.booking_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found.']);
        exit;
    }

    $userEmail = $booking['umak_email']; // User's email for the notification
    $firstName = $booking['first_name'];
    $lastName = $booking['last_name'];
    $serviceType = $booking['service_type'];
    $bookingDate = $booking['booking_date'];

    $formattedDate = DateTime::createFromFormat('Y-m-d', $bookingDate)->format('F j, Y');


    // Check if the name values are properly retrieved
    if (empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'Booking does not contain valid user name.']);
        exit;
    }

    // Perform the appropriate action based on the request
    if ($action === 'accept') {
        $sql = "UPDATE bookings SET status = 'Approved' WHERE booking_id = ?";
        $statusMessage = 'Your appointment has been approved.'; // Message to include in email body
    } elseif ($action === 'reject') {
        $sql = "DELETE FROM bookings WHERE booking_id = ?";
        $statusMessage = 'We regret to inform you that your appointment has been rejected.';
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        exit;
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ebuscato.k12043456@umak.edu.ph'; // Your email address
            $mail->Password = 'xuke uebp cnyk qfsw'; // Your email password (consider using app-specific password)
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
            $mail->addAddress($userEmail); // Send email to the user

            $mail->Subject = 'Booking Status';
            $mail->Body = "
                Dear $firstName $lastName,<br><br>
                $statusMessage<br><br>
                Service Type: $serviceType<br>
                Booking Date: $formattedDate<br><br>
                Regards,<br>
                MDOApp Admin
            ";  
            $mail->isHTML(true);
            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Booking updated and email sent.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Query execution failed.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
}

$conn->close();
?>
