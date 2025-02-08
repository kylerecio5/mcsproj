<?php
// Database connection
$host = "localhost";
$dbname = "tms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if resident_code is provided in the request
if (isset($_POST['resident_code'])) {
    $resident_code = $_POST['resident_code'];

    // Prepare the query to check if the resident code exists in tbl_residents
    $sql = "SELECT * FROM tbl_residents WHERE residentcode = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $resident_code);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching resident code exists
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['exists' => false]);
}

$conn->close();
?>
