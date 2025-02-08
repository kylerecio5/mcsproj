<?php
// remove_resident.php
header('Content-Type: application/json');
include('db_connection.php');

// Start the session
session_start();

// Get the data from the request
$data = json_decode(file_get_contents("php://input"), true);
$resident_id = $data['id']; // Using the correct column name 'Resident_ID'

// Fetch the resident's first and last name before deletion
$residentSql = "SELECT F_name, L_name FROM tbl_residents WHERE Resident_ID = ?";
$residentStmt = $conn->prepare($residentSql);
$residentStmt->bind_param("i", $resident_id);
$residentStmt->execute();
$residentStmt->bind_result($first_name, $last_name);
$residentStmt->fetch();
$residentStmt->close();

// Update the SQL query to delete the resident
$sql = "DELETE FROM tbl_residents WHERE Resident_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resident_id);

// Execute the query and send a response
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
    $description = "$user deleted resident: $first_name $last_name"; // Log description with resident's full name
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log into tbl_logs
    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
               VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
