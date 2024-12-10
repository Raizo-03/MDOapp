<?php
header('Content-Type: application/json');
require dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer's autoloader for PHPMailer
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$newStatus = $data['status'] ?? null;

// Validate inputs
if (!$userId || !$newStatus) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}
$sql = "SELECT umak_email, first_name, last_name FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $umakEmail = $user['umak_email'];
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];

    // Update the user status in the database
    $sql = "UPDATE Users SET status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $userId);

    if ($stmt->execute()) {
        // Send email notification using PHPMailer
        $mail = new PHPMailer(true);
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
            $mail->addAddress($umakEmail); 

            $statusMessage = ($newStatus === 'active') ? 'Your account has been reactivated.' : 'Your account has been deactivated.';
            $mail->Subject = 'Account Status Changed';
            $mail->Body    = "Dear $firstName $lastName,<br><br>$statusMessage<br><br>Regards,<br>MDOApp Admin";
            $mail->isHTML(true);

            $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update user status']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>