<?php
// Database connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "tms"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed.']));
}

// Get the reservation ID from the request
$reservation_id = isset($_GET['reservation_id']) ? intval($_GET['reservation_id']) : null;

// Check if reservation ID is valid
if ($reservation_id) {
    // Prepare the SQL statement
    $sql = "DELETE FROM tbl_reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $reservation_id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete reservation.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid reservation ID.']);
}

$conn->close();
?>
