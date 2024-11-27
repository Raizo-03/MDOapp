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

    if (!empty($title) && !empty($details)) {
        $stmt = $conn->prepare("INSERT INTO Announcements (title, details, image_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $details, $imageUrl);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Announcement added successfully!']);
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
        // Fetch the latest announcement (e.g., based on a timestamp or ID)
        $sql = "SELECT title, details, image_url FROM announcements ORDER BY id DESC LIMIT 1"; // Assuming `id` is auto-incremented
    } else {
        // Fetch all announcements
        $sql = "SELECT title, details, image_url FROM announcements";
    }

    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        if ($type === 'latest') {
            // Fetch the single latest announcement
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
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>