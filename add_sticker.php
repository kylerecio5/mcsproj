<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your database connection
include 'db_connection.php';

// Start the session for logging user actions
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $plate_number = $_POST['plate_number'];
    $sticker_number = $_POST['sticker_number'];
    $amount = $_POST['amount'];

    // Concatenate first name, middle name, and last name into a single name field
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    // Prepare the SQL query to insert the sticker data
    $sql = "INSERT INTO tbl_stickers (NAME, PHONE_NO, DATE, VehicleType, PlateNum, StickerNum, Amount) 
            VALUES (?, ?, CURDATE(), ?, ?, ?, ?)";

    // Check if the query can be prepared
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ssssss", $full_name, $phone_number, $vehicle_type, $plate_number, $sticker_number, $amount);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Get the last inserted Sticker_ID
            $sticker_id = $conn->insert_id;

            // Log the action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Add Sticker';
            $description = "$user added sticker with Sticker ID: $sticker_id and Plate Number: $plate_number";
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // Insert log into tbl_logs
            $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                       VALUES (?, ?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
            $logStmt->execute();
            $logStmt->close();

            // Return success response in JSON format
            echo json_encode(["success" => true, "message" => "Sticker added successfully!"]);
        } else {
            // Return error response in JSON format
            echo json_encode(["success" => false, "message" => "Error adding sticker: " . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Return error response in JSON format if the statement couldn't be prepared
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    }

    // Close the database connection
    $conn->close();
}
?>
