<?php
session_start(); 
include 'db_connection.php'; // Adjust the path if necessary

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $name = $_POST['Name'] ?? '';
    $phoneNo = $_POST['PhoneNo'] ?? '';
    $vehicleType = $_POST['vehicle_type'] ?? '';
    $plateNo = $_POST['Plate_No'] ?? '';  // Ensure this is the correct key
    $parkingType = $_POST['parking_type'] ?? '';
    $amount = $_POST['Amount'] ?? '';

    // Debugging: Check if PlateNo is coming through as expected
    error_log("Received PlateNo: $plateNo");
    error_log("Received Name: $name");

    try {
        // SQL Query - Ensure case-sensitive column names match the database
        $sql = "UPDATE tbl_parking 
                SET NAME = ?, VEHICLE_TYPE = ?, PARKING_TYPE = ?, AMOUNT = ?, PHONE_NO = ?, PLATE_NO = ?
                WHERE NAME = ?"; // Matching the NAME in the WHERE clause to the old NAME for the update

        if ($stmt = $conn->prepare($sql)) {
            // Debugging: Check if parameters are correctly passed
            error_log("Binding parameters: $name, $vehicleType, $parkingType, $amount, $phoneNo, $plateNo");

            // Bind the parameters to the SQL query
            $stmt->bind_param("sssssss", $name, $vehicleType, $parkingType, $amount, $phoneNo, $plateNo, $name);

            // Execute the query
            if ($stmt->execute()) {
                // Log the update details
                if (isset($_SESSION['user']['account_id'])) {
                    $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                    $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
                } else {
                    $accountId = null;
                    $user = 'Unknown User';
                }

                // Prepare log description
                $action = 'Update Parking Record';
                $description = "$user updated parking record for $name (Plate: $plateNo, Vehicle Type: $vehicleType, Parking Type: $parkingType, Amount: $amount)"; // Log details
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
                $response['success'] = true;
                $response['message'] = "Parking updated successfully.";
            } else {
                $response['message'] = "Failed to update parking. MySQL error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $response['message'] = "Failed to prepare the statement. MySQL error: " . $conn->error;
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
}

// Close the database connection
$conn->close();

// Return the response as JSON
echo json_encode($response);
?>
