<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the posted data (prices)
$data = json_decode(file_get_contents('php://input'), true);

// Update prices based on facility name
foreach ($data as $price) {
    $facility = $price['facility'];
    $day_rate = floatval($price['day']);
    $night_rate = floatval($price['night']);

    // Check if the facility already exists
    $sql = "SELECT id FROM tbl_bookingprices WHERE facility_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $facility);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the facility exists, update the rates
        $sql = "UPDATE tbl_bookingprices SET day_rate = ?, night_rate = ? WHERE facility_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dds", $day_rate, $night_rate, $facility);
    } else {
        // If the facility doesn't exist, insert a new record
        $sql = "INSERT INTO tbl_bookingprices (facility_name, day_rate, night_rate) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdd", $facility, $day_rate, $night_rate);
    }

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error saving prices.']);
        exit;
    }
}

// Close connection
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'message' => 'Prices saved successfully.']);
?>
