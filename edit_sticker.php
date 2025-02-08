<?php
ini_set('log_errors', 1); 
ini_set('error_log', __DIR__ . '/error_log.txt'); // Log file in the same directory
error_reporting(E_ALL);
// Set the response type to JSON
header('Content-Type: application/json');

// Include database connection
include 'db_connection.php';

// Start the session for logging user actions
session_start();

// Log the POST data for debugging
error_log(print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $sticker_id = $_POST['sticker_id'] ?? null;
    $first_name = $_POST['first_name'] ?? null;
    $middle_name = $_POST['middle_name'] ?? null;
    $last_name = $_POST['last_name'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $vehicle_type = $_POST['vehicle_type'] ?? null;
    $plate_number = $_POST['plate_number'] ?? null;
    $sticker_number = $_POST['sticker_number'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $date = $_POST['date'] ?? null;

    // Combine first, middle, and last names into one
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    // SQL query to update sticker
    $sql = "UPDATE tbl_stickers 
            SET NAME = ?, PHONE_NO = ?, VehicleType = ?, PlateNum = ?, StickerNum = ?, Amount = ?, DATE = ?
            WHERE Sticker_ID = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("sssssssi", $full_name, $phone_number, $vehicle_type, $plate_number, $sticker_number, $amount, $date, $sticker_id);

        // Execute the query
        if ($stmt->execute()) {
            // Log the action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Update Sticker';
            $description = "$user updated sticker with Sticker ID: $sticker_id and Plate Number: $plate_number"; // Log details
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
            echo json_encode(["success" => true, "message" => "Sticker updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating sticker: " . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    }

    // Close database connection
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
