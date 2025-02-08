<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php';

// Set the debug file path
$debug_file = 'debug_output.txt'; 

// Function to log messages to the debug file
function log_to_debug_file($message) {
    global $debug_file;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($debug_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Check if the user is logged in
if (!isset($_SESSION['user']['account_id'])) {
    $message = 'User not logged in.';
    log_to_debug_file($message);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$accountId = $_SESSION['user']['account_id'];
log_to_debug_file("User logged in with account_id: $accountId");

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Check if new_password is provided
    if (isset($input['new_password']) && !empty($input['new_password'])) {
        $newPassword = $input['new_password'];
        log_to_debug_file("Received new password input.");

        // Validate password strength (optional)
        if (strlen($newPassword) < 6) {
            $error_msg = 'Password must be at least 6 characters long.';
            log_to_debug_file($error_msg);
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        }

        // Hash the new password
        $newPasswordHash = $newPassword; // No hashing, as per your request
        log_to_debug_file("Prepared new password for update (no hashing applied).");

        // Prepare the SQL statement to update password in tbl_accounts
        $stmt1 = $conn->prepare("UPDATE tbl_accounts SET password = ? WHERE account_id = ?");
        $stmt1->bind_param("si", $newPasswordHash, $accountId);

        // Log query before execution
        log_to_debug_file("Executing query to update password in tbl_accounts for account_id = $accountId.");

        // Execute the query for tbl_accounts
        if ($stmt1->execute()) {
            if ($stmt1->affected_rows > 0) {
                log_to_debug_file("Password updated successfully in tbl_accounts for account_id = $accountId.");
            } else {
                log_to_debug_file("No rows affected in tbl_accounts for account_id = $accountId.");
            }

            // If the user is an admin, also update the password in tbl_admin
            $stmt2 = $conn->prepare("UPDATE tbl_admin SET admin_password = ? WHERE admin_id = (SELECT admin_id FROM tbl_accounts WHERE account_id = ?)");
            $stmt2->bind_param("si", $newPasswordHash, $accountId);

            // Log query before execution
            log_to_debug_file("Executing query to update password in tbl_admin for admin_id related to account_id = $accountId.");

            if ($stmt2->execute()) {
                if ($stmt2->affected_rows > 0) {
                    log_to_debug_file("Password updated successfully in tbl_admin for admin_id related to account_id = $accountId.");
                    echo json_encode(['success' => true, 'message' => 'Password updated successfully in both tables.']);
                } else {
                    log_to_debug_file("No rows affected in tbl_admin for admin_id related to account_id = $accountId.");
                    echo json_encode(['success' => true, 'message' => 'Password updated successfully in tbl_accounts, but no changes in tbl_admin.']);
                }
            } else {
                $error_msg = "Failed to update password in tbl_admin. Error: " . $stmt2->error;
                log_to_debug_file($error_msg);
                echo json_encode(['success' => false, 'message' => $error_msg]);
            }

            $stmt2->close();
        } else {
            $error_msg = "Failed to update password in tbl_accounts. Error: " . $stmt1->error;
            log_to_debug_file($error_msg);
            echo json_encode(['success' => false, 'message' => $error_msg]);
        }

        $stmt1->close();
    } else {
        $error_msg = 'New password is required.';
        log_to_debug_file($error_msg);
        echo json_encode(['success' => false, 'message' => $error_msg]);
    }
} else {
    $error_msg = 'Invalid request method.';
    log_to_debug_file($error_msg);
    echo json_encode(['success' => false, 'message' => $error_msg]);
}

$conn->close();
?>
