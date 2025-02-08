<?php
session_start();
// Database connection
$host = 'localhost';
$username = 'root'; // Database username
$password = ''; // Database password
$dbname = 'tms'; // Your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Check if the necessary data is provided
if (isset($data['account_id']) && isset($data['new_password'])) {
    $accountId = $data['account_id'];
    $newPassword = $data['new_password']; // No hashing, store as plain text

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE tbl_accounts SET password = ? WHERE account_id = ?");
    $stmt->bind_param('si', $newPassword, $accountId);

    if ($stmt->execute()) {
        // Log the password update action
        if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
            $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
        } else {
            // Fallback in case session variables are incomplete
            $user = 'Unknown User';
        }
        $action = 'Update Password';
        $description = "$user updated the password for account ID $accountId";
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Insert log into tbl_logs
        $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                   VALUES (?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
        $logStmt->execute();
        $logStmt->close();

        // Send success response
        echo json_encode(['success' => true]);
    } else {
        // Send error response
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
}

$conn->close();
?>
