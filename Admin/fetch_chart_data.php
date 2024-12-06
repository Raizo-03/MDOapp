<?php
header("Content-Type: application/json");
$jawsdb_url = parse_url(getenv("JAWSDB_URL")); // Use the JAWSDB_URL environment variable
$jawsdb_server = $jawsdb_url["host"];
$jawsdb_username = $jawsdb_url["user"];
$jawsdb_password = $jawsdb_url["pass"];
$jawsdb_db = substr($jawsdb_url["path"], 1); // Remove the leading '/' from the path

// Connect to the database
$conn = mysqli_connect($jawsdb_server, $jawsdb_username, $jawsdb_password, $jawsdb_db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['chart_data'])) {
    $query = $conn->prepare("
        SELECT
            service_type, 
            YEAR(booking_date) AS year, 
            MONTH(booking_date) AS month, 
            COUNT(*) AS total 
        FROM Bookings 
        WHERE status = 'Completed' 
        GROUP BY YEAR(booking_date), MONTH(booking_date), service_type
        ORDER BY YEAR(booking_date), MONTH(booking_date)
    ");
    $query->execute();
    $result = $query->get_result();
    $chartData = [];
    while ($row = $result->fetch_assoc()) {
        $chartData[] = $row; // Include year, month, and total counts
    }
    echo json_encode($chartData);
    exit();
} else {
    echo json_encode(["error" => "Invalid request"]);
}
$conn->close();
?>