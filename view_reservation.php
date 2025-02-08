<?php
// Database connection
$servername = "localhost"; // Change this to your DB server
$username = "root"; // DB username
$password = ""; // DB password
$dbname = "tms"; // DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the reservation_id from the URL
$reservation_id = isset($_GET['reservation_id']) ? $_GET['reservation_id'] : null;

// Check if reservation_id is provided
if ($reservation_id) {
    // Prepare the SQL query to fetch the reservation details
    $sql = "SELECT * FROM tbl_reservations WHERE reservation_id = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind the parameter
    $stmt->bind_param("i", $reservation_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Get the result
        $result = $stmt->get_result();
        
        // Check if any row is returned
        if ($result->num_rows > 0) {
            // Fetch the reservation data
            $reservation = $result->fetch_assoc();
            
            // Prepare the response
            $response = [
                'date' => $reservation['date'],
                'time' => $reservation['time'],
                'amenity' => $reservation['amenities'],
                'additionals' => "Chair: {$reservation['chair']}\nTable: {$reservation['table']}\nKaraoke: {$reservation['karaoke']}",
                'amount' => "₱" . number_format($reservation['Total_Price'], 2),
                'note' => $reservation['Note'], // Add the Note to the response
                'receipt_image_url' => !empty($reservation['Receipt']) ? $reservation['Receipt'] : "placeholderfinal1.png" // Fetch the actual file path
            ];
            
            // Send response as JSON
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'No records found for reservation ID: ' . $reservation_id]);
        }
    } else {
        echo json_encode(['error' => 'Database query failed.']);
    }
    
    // Close the statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'Reservation ID is missing.']);
}

// Close the database connection
$conn->close();
?>
