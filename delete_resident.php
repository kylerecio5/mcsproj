<?php
// Start the session
session_start();
require 'db_connection.php'; // Adjust as necessary to include your DB connection

// Check if ID is provided via GET
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Resident ID is missing']);
    exit;
}

$id = $_GET['id']; // Resident ID to delete

// Prepare the SQL query to delete the resident
$sql = "DELETE FROM tbl_residents WHERE Resident_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Log the action of the user performing the delete
    if (isset($_SESSION['user']['account_id'])) {
        $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
        $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
    } else {
        $accountId = null;
        $user = 'Unknown User';
    }

    $action = 'Delete Resident';
    $description = "$user deleted resident with ID $id"; // Log description with user's full name
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log into tbl_logs
    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
               VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete resident"]);
}

// Close connections
$stmt->close();
$conn->close();
?>
