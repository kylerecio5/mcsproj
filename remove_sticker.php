<?php
// remove_sticker.php
include 'db_connection.php';

// Start session to capture user details
session_start();

try {
    // Use PDO for database connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the input data (JSON payload from fetch)
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate that the sticker ID is provided
    if (isset($data['stickerId']) && !empty($data['stickerId'])) {
        $stickerId = $data['stickerId'];

        try {
            // First, retrieve the plate number for the sticker to log it
            $stmt = $pdo->prepare("SELECT PlateNum FROM tbl_stickers WHERE Sticker_ID = :sticker_id");
            $stmt->bindParam(':sticker_id', $stickerId, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch the plate number
            $plateNum = $stmt->fetchColumn();

            // Check if the sticker was found
            if (!$plateNum) {
                echo json_encode(['success' => false, 'message' => 'Sticker not found.']);
                exit;
            }

            // Prepare and execute the DELETE query to remove the sticker
            $stmt = $pdo->prepare("DELETE FROM tbl_stickers WHERE Sticker_ID = :sticker_id");
            $stmt->bindParam(':sticker_id', $stickerId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Log the sticker removal action
                if (isset($_SESSION['user']['account_id'])) {
                    $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                    $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
                } else {
                    $accountId = null;
                    $user = 'Unknown User';
                }

                // Prepare log description
                $action = 'Remove Sticker';
                $description = "$user removed sticker with Sticker ID: $stickerId and Plate Number: $plateNum"; // Log details
                $date = date('Y-m-d');
                $time = date('H:i:s');

                // Insert log into tbl_logs
                $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                           VALUES (?, ?, ?, ?, ?)";
                $logStmt = $pdo->prepare($logSql);
                $logStmt->bindParam(1, $accountId, PDO::PARAM_INT);
                $logStmt->bindParam(2, $action, PDO::PARAM_STR);
                $logStmt->bindParam(3, $description, PDO::PARAM_STR);
                $logStmt->bindParam(4, $date, PDO::PARAM_STR);
                $logStmt->bindParam(5, $time, PDO::PARAM_STR);
                $logStmt->execute();
                $logStmt->closeCursor();

                // Return success response in JSON format
                echo json_encode(['success' => true, 'message' => 'Sticker removed successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove sticker.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid sticker ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
