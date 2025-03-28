<?php
// Set content type to JSON
header('Content-Type: application/json');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to add a new announcement
    $title = $_POST['title'] ?? '';
    $details = $_POST['details'] ?? '';
    $imageUrl = $_POST['image_url'] ?? ''; // Optional image URL
    $status = $_POST['status'] ?? 'draft'; // Default status to draft

    if (!empty($title) && !empty($details)) {
        $stmt = $conn->prepare("INSERT INTO Announcements (title, details, image_url, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $details, $imageUrl, $status);

        if ($stmt->execute()) {
            $last_id = $conn->insert_id;  
            echo json_encode([
                'status' => 'success',
                'id' => $last_id,  // Return the last inserted ID
                'message' => 'Announcement added successfully!'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add announcement.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Title and details are required.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check for the 'type' query parameter to fetch either latest or all announcements
    $type = $_GET['type'] ?? 'all'; // Default to 'all' if no type is specified

    if ($type === 'latest') {
        // Fetch the latest published announcement
        $sql = "SELECT id, title, details, image_url FROM Announcements WHERE status = 'published' ORDER BY id DESC LIMIT 1";
    } else {
        // Fetch all published announcements
        $sql = "SELECT id, title, details, image_url FROM Announcements WHERE status = 'published'";
    }

    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        if ($type === 'latest') {
            // Fetch the single latest published announcement
            $announcement = $result->fetch_assoc();
            echo json_encode($announcement); // Return as an object
        } else {
            $announcements = [];
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $announcements[] = $row;
                }
            }
            echo json_encode($announcements); // Return as an array
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch announcements.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Read the raw POST data and decode JSON
    $data = json_decode(file_get_contents("php://input"), true); // true converts JSON to associative array
    $id = $data['id'] ?? 0; // Get the `id` to delete

    error_log("ID received for deletion: " . $id); // Write to log

    if (!empty($id)) {
        $stmt = $conn->prepare("DELETE FROM Announcements WHERE id = ?");
        $stmt->bind_param("i", $id); // Bind the `id` parameter as an integer

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Announcement deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete announcement or announcement not found.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid announcement ID.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
