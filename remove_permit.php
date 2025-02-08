<?php
session_start();
// remove_permit.php
require_once 'db_connection.php'; // Include the database connection

// Get the permit ID from the request
$data = json_decode(file_get_contents("php://input"), true);
$permitId = $data['id'];

if (empty($permitId)) {
    echo json_encode(["success" => false, "message" => "Permit ID is required"]);
    exit;
}

// Prepare SQL to retrieve permit details before deletion (for logging purposes)
$permitQuery = "SELECT Name, BuildingType, PermitNum FROM tbl_construction_permits WHERE Permit_ID = ?";
$permitStmt = $conn->prepare($permitQuery);
$permitStmt->bind_param("i", $permitId);
$permitStmt->execute();
$permitStmt->store_result();

if ($permitStmt->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Permit not found"]);
    exit;
}

$permitStmt->bind_result($name, $buildingType, $permitNum);
$permitStmt->fetch();

// Prepare SQL to delete the permit
$query = "DELETE FROM tbl_construction_permits WHERE Permit_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $permitId);

// Execute deletion
if ($stmt->execute()) {
    // Log the permit removal action
    if (isset($_SESSION['user']['account_id'])) {
        $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
        $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
    } else {
        $accountId = null;
        $user = 'Unknown User';
    }

    // Prepare log description
    $action = 'Remove Construction Permit';
    $description = "$user removed permit for $name (Building Type: $buildingType, Permit Number: $permitNum)"; // Log details
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log into tbl_logs
    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
               VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();

    echo json_encode(["success" => true, "message" => "Permit removed successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Error removing permit"]);
}

$stmt->close();
$permitStmt->close();
$conn->close();
?>
