<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Service durations configuration
$serviceDurations = [
    'Medical' => [
        'General Consultation' => 30,
        'Physical Examination' => 30,
        'Treatment for minor illness' => 30
    ],
    'Dental' => [
        'Tooth Extraction' => 60,
        'Teeth Cleaning' => 60,
        'Dental Fillings' => 60,
        'Dental Consultation' => 30
    ]
];

// Time slots configuration
$timeSlots = [
    '08:00:00' => '8-9 AM',
    '09:00:00' => '9-10 AM',
    '10:00:00' => '10-11 AM',
    '11:00:00' => '11-12 PM',
    '13:00:00' => '1-2 PM',
    '14:00:00' => '2-3 PM',
    '15:00:00' => '3-4 PM',
    '16:00:00' => '4-5 PM'
];

function getServiceDuration($serviceType, $service, $serviceDurations) {
    return isset($serviceDurations[$serviceType][$service]) ? 
           $serviceDurations[$serviceType][$service] : 30;
}

function convertTimeToMinutes($time) {
    $parts = explode(':', $time);
    return ($parts[0] * 60) + $parts[1];
}

function checkTimeSlotAvailability($conn, $date, $time, $newServiceDuration, $serviceDurations) {
    // Get all bookings for the specific date
    $stmt = $conn->prepare("SELECT booking_time, service, service_type FROM bookings WHERE booking_date = ? AND status IN ('Pending', 'Approved')");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $existingBookings = [];
    while ($row = $result->fetch_assoc()) {
        $existingDuration = getServiceDuration($row['service_type'], $row['service'], $serviceDurations);
        $existingBookings[] = [
            'time' => $row['booking_time'],
            'duration' => $existingDuration,
            'service' => $row['service'],
            'service_type' => $row['service_type']
        ];
    }
    
    $newTimeMinutes = convertTimeToMinutes($time);
    $newEndTime = $newTimeMinutes + $newServiceDuration;
    
    foreach ($existingBookings as $booking) {
        $existingTimeMinutes = convertTimeToMinutes($booking['time']);
        $existingEndTime = $existingTimeMinutes + $booking['duration'];
        
        // Check for overlap
        if (($newTimeMinutes < $existingEndTime) && ($newEndTime > $existingTimeMinutes)) {
            // There's a conflict
            if ($booking['duration'] == 30 && $newServiceDuration == 30) {
                // Both are 30 minutes - check if they can fit in the same hour
                if ($newTimeMinutes == $existingTimeMinutes) {
                    return [
                        'available' => false,
                        'canBook30Min' => false,
                        'message' => 'This time slot is already booked. Please choose another time.',
                        'conflictType' => 'EXACT_TIME_CONFLICT',
                        'conflictingBookings' => $existingBookings
                    ];
                } else {
                    // They can potentially fit in the same hour slot
                    return [
                        'available' => false,
                        'canBook30Min' => true,
                        'message' => 'This hour slot is partially booked. You can still book a 30-minute service, but consider booking another time for better availability.',
                        'conflictType' => 'PARTIAL_CONFLICT_30MIN',
                        'conflictingBookings' => $existingBookings
                    ];
                }
            } else {
                // One or both services are 60 minutes
                return [
                    'available' => false,
                    'canBook30Min' => false,
                    'message' => 'This time slot is already booked with a 1-hour service. Please choose another time.',
                    'conflictType' => 'FULL_CONFLICT_60MIN',
                    'conflictingBookings' => $existingBookings
                ];
            }
        }
    }
    
    return [
        'available' => true,
        'canBook30Min' => true,
        'message' => 'Time slot is available for booking.',
        'conflictType' => 'NO_CONFLICT',
        'conflictingBookings' => []
    ];
}

// Handle different API endpoints
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'check_availability':
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $service = $_POST['service'] ?? '';
        $serviceType = $_POST['service_type'] ?? '';
        
        if (empty($date) || empty($time) || empty($service) || empty($serviceType)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            break;
        }
        
        $serviceDuration = getServiceDuration($serviceType, $service, $serviceDurations);
        $availability = checkTimeSlotAvailability($conn, $date, $time, $serviceDuration, $serviceDurations);
        
        echo json_encode([
            'success' => true,
            'available' => $availability['available'],
            'canBook30Min' => $availability['canBook30Min'],
            'message' => $availability['message'],
            'service_duration' => $serviceDuration,
            'conflict_type' => $availability['conflictType'],
            'conflicting_bookings' => $availability['conflictingBookings']
        ]);
        break;
        
    case 'get_available_slots':
        $date = $_GET['date'] ?? '';
        
        if (empty($date)) {
            echo json_encode(['success' => false, 'message' => 'Date is required']);
            break;
        }
        
        $availableSlots = [];
        
        foreach ($timeSlots as $timeKey => $timeLabel) {
            // Check availability for both 30min and 60min services
            $availability30 = checkTimeSlotAvailability($conn, $date, $timeKey, 30, $serviceDurations);
            $availability60 = checkTimeSlotAvailability($conn, $date, $timeKey, 60, $serviceDurations);
            
            $availableSlots[$timeKey] = [
                'label' => $timeLabel,
                'available_30min' => $availability30['available'] || $availability30['canBook30Min'],
                'available_60min' => $availability60['available'],
                'message_30min' => $availability30['message'],
                'message_60min' => $availability60['message'],
                'conflict_type_30min' => $availability30['conflictType'],
                'conflict_type_60min' => $availability60['conflictType']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'available_slots' => $availableSlots
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>