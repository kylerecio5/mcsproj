<?php
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start(); // Start session to retrieve user info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $historyID = $_POST['idToRemove'];

    // Validate required fields
    if ($historyID !== null) {
        // Prepare the SQL delete statement
        $sql = "DELETE FROM tbl_history WHERE History_ID = ?";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $historyID);

        // Execute the query
        if ($stmt->execute()) {
            // Log the deletion action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log details
            $action = 'Delete Payment History';
            $description = "$user deleted payment history with History ID $historyID";
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // Insert log into the logs table
            $logSql = "INSERT INTO tbl_logs (Account_ID, Action, Description, Date, Time) VALUES (?, ?, ?, ?, ?)";
            $logStmt = $pdo->prepare($logSql);
            $logStmt->bindParam(1, $accountId);
            $logStmt->bindParam(2, $action);
            $logStmt->bindParam(3, $description);
            $logStmt->bindParam(4, $date);
            $logStmt->bindParam(5, $time);
            $logStmt->execute();

            // Redirect after successful deletion
            header('Location: monthly_dues.php');
            exit();
        } else {
            echo json_encode(["code" => 0]);
        }
    }
}
?>
