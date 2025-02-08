<?php
session_start(); 
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your database connection (Make sure you have db_connection.php properly set up)
include 'db_connection.php';

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data from the request
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $plate_number = $_POST['plate_number'];
    $parking_type = $_POST['parking_type'];
    $amount = $_POST['amount'];

    // Concatenate first name, middle name, and last name into one full name
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    // Prepare the SQL query to insert the parking data into the database
    $sql = "INSERT INTO tbl_parking (NAME, VEHICLE_TYPE, PLATE_NO, PARKING_TYPE, AMOUNT, PHONE_NO, DATE) 
            VALUES (?, ?, ?, ?, ?, ?, CURDATE())";

    // Check if the query can be prepared
    if ($stmt = $conn->prepare($sql)) {
        // Bind the form data to the prepared statement
        $stmt->bind_param("ssssds", $full_name, $vehicle_type, $plate_number, $parking_type, $amount, $phone_number);

        // Execute the statement and check if it was successful
        if ($stmt->execute()) {
            // Log the parking data (name, plate number, vehicle type, parking type)
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Add Parking Record';
            $description = "$user added parking record for $full_name (Plate: $plate_number, Vehicle Type: $vehicle_type, Parking Type: $parking_type)"; // Log details
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // Insert log into tbl_logs
            $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                       VALUES (?, ?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
            $logStmt->execute();
            $logStmt->close();

            // Return success response as JSON
            echo json_encode(["success" => true, "message" => "Parking added successfully."]);
        } else {
            // Return error response as JSON
            echo json_encode(["success" => false, "message" => "Error adding parking: " . $stmt->error]);
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Return error response if the statement couldn't be prepared
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    }

    // Close the database connection
    $conn->close();
} else {
    // Return error response if the request method is not POST
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
