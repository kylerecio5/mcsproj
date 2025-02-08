<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

$yearFilter = $_GET['year'];
$yearFilter = filter_var($yearFilter, FILTER_SANITIZE_NUMBER_INT);


// Fetch data from tbl_monthly_dues
$query = "SELECT r.residentcode, m.Dues_ID, r.resident_ID AS ID, CONCAT(r.F_name,' ',r.M_name,' ',r.L_name) as Resident, m.StreetLight, m.Amount, m.Status
FROM tbl_residents r 
JOIN tbl_monthly_dues m 
ON r.resident_ID = m.resident 
WHERE m.Year = :yearFilter";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':yearFilter', $yearFilter, PDO::PARAM_INT);
$stmt->execute();
$dues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if any data was fetched
if ($dues) {
    // Return data as JSON
    echo json_encode($dues);
} else {
    // Return an empty array if no data was found
    echo json_encode([]);
}

?>
