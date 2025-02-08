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
    $residents = $_POST['resident'];
    $streetLight = $_POST['street-light'];
    $monthlyAmount = $_POST['monthly_amount'];
    $year = $_POST['year'];


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $year = $_POST['year'];
        $street_light = $_POST['street-light'];
        $monthly_amount = $_POST['monthly_amount'];

        if (!empty($_POST['resident'])) {
            foreach ($_POST['resident'] as $resident_id) {
                $stmt = $pdo->prepare("INSERT INTO tbl_monthly_dues (Resident, Year, StreetLight, Amount) VALUES (?, ?, ?, ?)");
                $stmt->execute([$resident_id, $year, $street_light, $monthly_amount]);
            }
        }





        // Log the user's action
        if (isset($_SESSION['user']['account_id'])) {
            $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
            $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
        } else {
            $accountId = null;
            $user = 'Unknown User';
        }

        // Prepare log details
        $action = 'Add Monthly Dues Record';
        $description = "$user added monthly dues record for $residents with street light: " . ($streetLight ? 'Yes' : 'No');
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Prepare the SQL insert statement
        $sql = "INSERT INTO tbl_monthly_dues (Resident, StreetLight, Amount, Year) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $residents);
        $stmt->bindParam(2, $streetLight);
        $stmt->bindParam(3, $monthlyAmount);
        $stmt->bindParam(4, $year);

        // Execute the query
        if ($stmt->execute()) {
            // Log the action in the log table
            $logSql = "INSERT INTO tbl_logs (Account_ID, Action, Description, Date, Time) VALUES (?, ?, ?, ?, ?)";
            $logStmt = $pdo->prepare($logSql);
            $logStmt->bindParam(1, $accountId);
            $logStmt->bindParam(2, $action);
            $logStmt->bindParam(3, $description);
            $logStmt->bindParam(4, $date);
            $logStmt->bindParam(5, $time);
            $logStmt->execute();

            // Redirect to the monthly dues page
            header("Location: monthly_dues.php");
            exit();
        }
    } else {
        echo "Error adding record.";
    }
}
?>