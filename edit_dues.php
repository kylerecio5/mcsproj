<?php
// Database connection parameters
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
    $streetLight = $_POST['edit_street-light'];
    $monthlyAmount = $_POST['edit_monthly_amount'];
    $duesID = $_POST['idToEdit'];

    // Validate required fields
    if ($streetLight && $monthlyAmount !== null) {
        // Prepare the SQL update statement
        $sql = "UPDATE tbl_monthly_dues 
                SET StreetLight=?, Amount=?
                WHERE Dues_ID = ?";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $streetLight);
        $stmt->bindParam(2, $monthlyAmount);
        $stmt->bindParam(3, $duesID);

        // Execute the update query
        if ($stmt->execute()) {
            // Log the update action
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log details
            $action = 'Update Monthly Dues Record';
            $description = "$user updated monthly dues record with Dues_ID $duesID. Street light: " . ($streetLight ? 'Yes' : 'No') . ", Amount: $monthlyAmount";
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

            // Redirect after successful update
            header('Location: monthly_dues.php');
            exit();
        } else {
            echo "Error updating record.";
        }
    } else {
        echo "Invalid input data.";
    }
}
?>
