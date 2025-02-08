<?php
// Debugging: create or open the log file
$logFile = 'debug_log.txt';
if (!file_exists($logFile)) {
    file_put_contents($logFile, "Debug log created at " . date('Y-m-d H:i:s') . "\n\n");
}

// Start the session
session_start();
require 'db_connection.php'; // Adjust as necessary to include your DB connection

// Log debug information
function logDebug($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] - $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Debug: Check if POST data exists
logDebug("Received POST data: " . json_encode($_POST));

if (!isset($_POST['Resident_ID'])) {
    logDebug("Error: Resident_ID is missing in POST data.");
    echo json_encode(['success' => false, 'message' => 'Resident ID is missing']);
    exit;
}

// Retrieve POST data
$residentId = $_POST['Resident_ID']; // Make sure the key is 'Resident_ID'
$firstName = $_POST['F_name'];
$middleName = $_POST['M_name'];
$lastName = $_POST['L_name'];
$phoneNumber = $_POST['PhoneNum'];
$age = $_POST['Age'];
$sex = $_POST['Sex'];
$memberType = $_POST['MemberType'];
$membership = $_POST['Membership'];
$block = $_POST['Block'];
$lot = $_POST['Lot'];
$street = $_POST['Street'];

// Debug: Log variables
logDebug("Updating resident with ID: $residentId");

// Prepare the SQL query
$sql = "UPDATE tbl_residents SET
            F_name = ?, 
            M_name = ?, 
            L_name = ?, 
            PhoneNum = ?, 
            Age = ?, 
            Sex = ?, 
            MemberType = ?, 
            Membership = ?, 
            Block = ?, 
            Lot = ?, 
            Street = ?
        WHERE Resident_ID = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    logDebug("SQL prepare error: " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'SQL prepare error: ' . $conn->error]);
    exit;
}

// Bind parameters (11 fields to update, 1 for WHERE clause)
$stmt->bind_param(
    "ssssisssssss", 
    $firstName, 
    $middleName, 
    $lastName, 
    $phoneNumber, 
    $age, 
    $sex, 
    $memberType, 
    $membership, 
    $block, 
    $lot, 
    $street, 
    $residentId // Ensure Resident_ID is last
);

// Execute the statement
if ($stmt->execute()) {
    // Log success action
    logDebug("Resident with ID $residentId updated successfully.");

    // Log the action of the user performing the update
    if (isset($_SESSION['user']['account_id'])) {
        $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
        $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
    } else {
        $accountId = null;
        $user = 'Unknown User';
    }

    $action = 'Edit Resident';
    $description = "$user edited resident $firstName $lastName"; // Log description with user's full name
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log into tbl_logs
    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
               VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(['success' => true, 'message' => 'Resident updated successfully!']);
} else {
    logDebug("Error updating resident: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error updating resident: ' . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
