<?php
session_start();
// Include database connection
require 'db_connection.php';

// Get JSON payload from request
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['accountId'])) {
    $accountId = intval($input['accountId']); // Sanitize input

    // Prevent deleting the last admin account
    $adminCountQuery = "SELECT COUNT(*) as adminCount FROM tbl_accounts WHERE role = 'Admin' AND is_deleted = 0";
    $adminCountResult = $conn->query($adminCountQuery);

    if ($adminCountResult) {
        $adminCount = $adminCountResult->fetch_assoc()['adminCount'];

        // Check if the account being deleted is an admin
        $checkAdminQuery = "SELECT username, role FROM tbl_accounts WHERE account_id = ? AND is_deleted = 0";
        $stmt = $conn->prepare($checkAdminQuery);

        if ($stmt) {
            $stmt->bind_param("i", $accountId);
            $stmt->execute();
            $roleResult = $stmt->get_result();

            if ($roleResult->num_rows > 0) {
                $account = $roleResult->fetch_assoc();
                $username = $account['username'];
                $role = $account['role'];

                // Ensure we are not deleting the last admin
                if ($role === 'Admin' && $adminCount <= 1) {
                    echo json_encode(['success' => false, 'error' => 'Cannot delete the last admin account.']);
                    exit;
                }

                // Step 1: Soft delete the account by setting is_deleted = 1
                $softDeleteQuery = "UPDATE tbl_accounts SET is_deleted = 1 WHERE account_id = ?";
                $softDeleteStmt = $conn->prepare($softDeleteQuery);

                if ($softDeleteStmt) {
                    $softDeleteStmt->bind_param("i", $accountId);

                    if ($softDeleteStmt->execute()) {
                        // Step 2: Log the deletion action
                        
                        if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
                            $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
                        } else {
                            // Fallback in case session variables are incomplete
                            $user = 'Unknown User';
                        }
                        $action = 'Remove Account';
                        $description = "$user removed account $username"; // Updated log description with full name
                        $date = date('Y-m-d');
                        $time = date('H:i:s');

                        // Insert log into tbl_logs
                        $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                                   VALUES (?, ?, ?, ?, ?)";
                        $logStmt = $conn->prepare($logSql);
                        $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);

                        if ($logStmt->execute()) {
                            $logStmt->close();
                        } else {
                            // Log an error if insertion fails
                            echo json_encode(['success' => false, 'error' => 'Failed to log the deletion action.']);
                            exit;
                        }

                        // Return success response
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to update account.']);
                    }

                    $softDeleteStmt->close();
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to prepare the soft delete statement.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Account not found or already deleted.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to prepare the check admin statement.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch admin count.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}

$conn->close();
?>
