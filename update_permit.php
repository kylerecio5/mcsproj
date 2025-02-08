<?php
session_start();
include 'db_connection.php'; // Include database connection

// Initialize the response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $name = $_POST['Name'] ?? '';
    $phoneNo = $_POST['PhoneNo'] ?? '';
    $buildingType = $_POST['BuildingType'] ?? '';
    $permitNum = $_POST['PermitNum'] ?? '';
    $permitId = $_POST['Permit_ID'] ?? '';  // Ensure that Permit_ID is included
    $block = $_POST['Block'] ?? '';
    $lot = $_POST['Lot'] ?? '';
    $street = $_POST['Street'] ?? '';
    $amount = $_POST['Amount'] ?? '';

    // Check if Permit_ID is set and valid
    if (empty($permitId)) {
        $response['message'] = "Permit ID is required.";
        echo json_encode($response);
        exit;
    }

    try {
        // Update query
        $sql = "UPDATE tbl_construction_permits
                SET 
                    Name = ?,
                    PhoneNo = ?,
                    BuildingType = ?,
                    PermitNum = ?, 
                    Block = ?,
                    Lot = ?,
                    Street = ?,
                    Amount = ?
                WHERE Permit_ID = ?";  // Use Permit_ID to uniquely identify the record

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        // Correct the bind_param types based on the number of variables
        $stmt->bind_param("ssssssssd", $name, $phoneNo, $buildingType, $permitNum, $block, $lot, $street, $amount, $permitId);

        // Execute the query
        if ($stmt->execute()) {
            // Log the update action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Update Construction Permit';
            $description = "$user updated permit for $name (Building Type: $buildingType, Permit Number: $permitNum)"; // Log details
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // Insert log into tbl_logs
            $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                       VALUES (?, ?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
            $logStmt->execute();
            $logStmt->close();

            // Set success response
            $response['success'] = true;
            $response['message'] = "Permit updated successfully.";
        } else {
            $response['message'] = "Failed to update permit.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
}

$conn->close();
echo json_encode($response);
?>
