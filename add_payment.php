<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

echo "Payment history added successfully.";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start(); // Start session to retrieve user info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data 
    $paymentDate = $_POST['payment-date']; // The resident's name
    $paymentMonth = $_POST['month']; // The street light
    $paymentAmount = $_POST['add_amount']; // The payment date
    $resident = $_POST['add_payment_residentID']; // The payment month
    $year = $_POST['year']; // The payment month

    // Check if a Dues_ID was found
    $sql = "INSERT INTO tbl_history (Dues_ID, Date, Month, Amount, Resident_ID, Year) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(1, $resident);
    $stmt->bindParam(2, $paymentDate);
    $stmt->bindParam(3, $paymentMonth);
    $stmt->bindParam(4, $paymentAmount);
    $stmt->bindParam(5, $resident);
    $stmt->bindParam(6, $year);

    // Execute the insertion
    if ($stmt->execute()) {
        // Log the action
        if (isset($_SESSION['user']['account_id'])) {
            $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
            $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
        } else {
            $accountId = null;
            $user = 'Unknown User';
        }

        // Prepare log details
        $action = 'Add Payment History';
        $description = "$user added a payment history for Resident ID $resident. Payment Amount: $paymentAmount, Payment Month: $paymentMonth";
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

        // Redirect after successful insertion
        header('Location: monthly_dues.php');
        exit();
    } else {
        echo "Error adding payment history.";
    }
}
?>
