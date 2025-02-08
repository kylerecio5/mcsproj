<?php
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([])); // Return empty JSON if the connection fails
}

$year = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Fetch residents who do not have an entry for the selected year
$query = "SELECT r.Resident_ID, r.F_name, r.M_name, r.L_name
          FROM tbl_residents r
          WHERE NOT EXISTS (
              SELECT 1 FROM tbl_monthly_dues m 
              WHERE m.Resident = r.Resident_ID AND m.Year = :year
          )";
$stmt = $pdo->prepare($query);
$stmt->execute(['year' => $year]);
$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($residents);
?>