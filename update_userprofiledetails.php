<?php
// Include database connection
require 'db.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get operation type (insert or update)
    $operation = $_POST['operation'] ?? '';

    // Retrieve the input values
    $umak_email = $_POST['umak_email'] ?? null;
    $contact_number = $_POST['contact_number'] ?? null;
    $address = $_POST['address'] ?? null;
    $guardian_contact_number = $_POST['guardian_contact_number'] ?? null;
    $guardian_address = $_POST['guardian_address'] ?? null;

    // Validate required fields
    if (!$umak_email) {
        echo json_encode(['success' => false, 'message' => 'umak_email is required']);
        exit;
    }

    // Choose the operation
    if ($operation === 'insert') {
        // Insert values into UserProfile
        $query = "INSERT INTO UserProfile (umak_email, contact_number, address, guardian_contact_number, guardian_address)
                  VALUES (?, ?, ?, ?, ?);";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $umak_email, $contact_number, $address, $guardian_contact_number, $guardian_address);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Record inserted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert record: ' . $stmt->error]);
        }
    } elseif ($operation === 'update') {
        // Update values in UserProfile
        $query = "UPDATE UserProfile SET contact_number = ?, address = ?, guardian_contact_number = ?, guardian_address = ?
                  WHERE umak_email = ?;";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $contact_number, $address, $guardian_contact_number, $guardian_address, $umak_email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No matching record found to update']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update record: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid operation']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
