<?php
session_start(); 
// Include database connection
require_once 'db_connection.php';

header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get the raw POST data and decode it
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'No parking ID provided.']);
    exit;
}

$parkingName = $data['id'];

try {
    // Prepare a SQL statement to delete the parking entry
    $sql = "DELETE FROM tbl_parking WHERE Name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $parkingName);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Log the deletion action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Delete Parking Record';
            $description = "$user deleted parking record for $parkingName"; // Log details
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
            echo json_encode(['success' => true, 'message' => 'Parking information removed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Parking information not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove parking information.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

// Close the database connection
$conn->close();
?>
