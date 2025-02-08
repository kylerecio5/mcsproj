<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

try {
    // Get form data (for example)
    $resident = $_POST['resident']; // The resident's name
    $streetLight = $_POST['street_light']; // The street light
    $paymentDate = $_POST['payment_date']; // The payment date
    $paymentMonth = $_POST['payment_month']; // The payment month
    $paymentAmount = $_POST['payment_amount']; // The payment amount

    // Step 1: Fetch the Dues_ID from tbl_monthly_due using Resident and StreetLight
    $sql = "SELECT Dues_ID FROM tbl_monthly_due WHERE Resident = :resident AND StreetLight = :street_light";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':resident', $resident);
    $stmt->bindParam(':street_light', $streetLight);
    $stmt->execute();

    // Check if a Dues_ID was found
    if ($stmt->rowCount() > 0) {
        $duesId = $stmt->fetch(PDO::FETCH_ASSOC)['Dues_ID'];

        // Step 2: Insert the record into tbl_history using the Dues_ID
        $sql = "INSERT INTO tbl_history (Dues_ID, Date, Month, Amount) 
                VALUES (:dues_id, :payment_date, :payment_month, :payment_amount)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':dues_id', $duesId);
        $stmt->bindParam(':payment_date', $paymentDate);
        $stmt->bindParam(':payment_month', $paymentMonth);
        $stmt->bindParam(':payment_amount', $paymentAmount);

        // Execute the insertion
        if ($stmt->execute()) {
            echo "Payment history added successfully.";
        } else {
            echo "Error adding payment history.";
        }
    } else {
        echo "No matching dues record found.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>