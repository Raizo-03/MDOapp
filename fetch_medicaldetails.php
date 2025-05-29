<?php
header("Content-Type: application/json");

// Parse JAWSDB connection details
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    
    // Initialize response array
    $response = array();
    
    // Fetch medical records
    $query_medical = $conn->prepare("SELECT diagnosis, complaint, prescription, doctor, notes, doctor_id 
                                    FROM medical_records 
                                    WHERE booking_id = ?");
    $query_medical->bind_param("i", $booking_id);
    $query_medical->execute();
    $result_medical = $query_medical->get_result();
    
    if ($row_medical = $result_medical->fetch_assoc()) {
        $response['medical_records'] = $row_medical;
    } else {
        $response['medical_records'] = null;
    }
    
    // Fetch vital signs
    $query_vitals = $conn->prepare("SELECT height_cm, weight_kg, blood_pressure, temperature_c, 
                                   attending_nurse, notes 
                                   FROM vital_signs 
                                   WHERE booking_id = ?");
    $query_vitals->bind_param("i", $booking_id);
    $query_vitals->execute();
    $result_vitals = $query_vitals->get_result();
    
    if ($row_vitals = $result_vitals->fetch_assoc()) {
        $response['vital_signs'] = $row_vitals;
    } else {
        $response['vital_signs'] = null;
    }
    
    // Return the results as JSON
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Invalid request. Booking ID required."]);
}

?>