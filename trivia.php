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
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Handle POST request to insert new trivia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the title and details from the POST request
    $title = $_POST['title'] ?? '';
    $text = $_POST['details'] ?? '';

    // Validate input
    if (!empty($title) && !empty($text)) {
        // Prepare SQL statement to insert new trivia
        $stmt = $conn->prepare("INSERT INTO Trivia (question, answer) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $text);

        // Execute and check for success
        if ($stmt->execute()) {
            $last_id = $conn->insert_id;  
            echo json_encode(['status' => 'success','id' => $last_id, 'message' => 'Trivia added successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add trivia.']);
        }

        $stmt->close(); // Close the prepared statement
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Title and text are required!']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the request is for the latest trivia or all trivia
    $type = $_GET['type'] ?? 'all'; // Default to 'all'

    if ($type === 'latest') {
        // Fetch the latest trivia
        $sql = "SELECT question AS title, answer AS details FROM Trivia ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $latestTrivia = $result->fetch_assoc();
            echo json_encode($latestTrivia); // Return the latest trivia as JSON
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No trivia found.']);
        }
    } elseif ($type === 'all') {
        // Fetch all trivia
        $sql = "SELECT id, question AS title, answer AS details FROM Trivia";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $trivia = [];
            while ($row = $result->fetch_assoc()) {
                $trivia[] = $row;
            }
            echo json_encode($trivia); // Return all trivia as JSON
        } else {
            echo json_encode([]); // Return empty array if no trivia found
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid type parameter.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Read the raw POST data and decode JSON
  $data = json_decode(file_get_contents("php://input"), true); // true converts JSON to associative array
  $id = $data['id'] ?? 0; // Get the `id` to delete

  error_log("ID received for deletion: " . $id); // Write to log

  if (!empty($id)) {
      $stmt = $conn->prepare("DELETE FROM Trivia WHERE id = ?");
      $stmt->bind_param("i", $id); // Bind the `id` parameter as an integer

      if ($stmt->execute() && $stmt->affected_rows > 0) {
          echo json_encode(['status' => 'success', 'message' => 'trivia deleted successfully!']);
      } else {
            error_log('Delete failed. Affected rows: ' . $stmt->affected_rows);
          echo json_encode(['status' => 'error', 'message' => 'Failed to delete trivia or trivia not found.']);
      }

      $stmt->close();
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Invalid trivia ID.']);
  }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>