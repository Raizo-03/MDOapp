<?php
header('Content-Type: application/json');

// Get database credentials from JAWSDB_URL environment variable
$jawsdb_url = parse_url(getenv("JAWSDB_URL"));
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove leading '/' from the path

// Connect to the database
$conn = new mysqli($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Get the raw POST data
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->id)) {
        $trivia_id = $data->id;

        // Prepare the DELETE query for the Trivia table
        $query = "DELETE FROM Trivia WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $trivia_id);

            // Execute the query
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Trivia deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete trivia.']);
            }

            // Close the statement
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Trivia ID missing.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
