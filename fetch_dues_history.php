<?php
$host = 'localhost';
$dbname = 'tms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

// Check if 'user' parameter is provided
if (isset($_GET['user']) && !empty($_GET['user'])) {
    $user = $_GET['user'];


    //To get year parameter - 01/1/2025
    //$year = $_GET['year'];

    // Filter the input to prevent security vulnerabilities
    $user = filter_var($user, FILTER_SANITIZE_NUMBER_INT);

    // Validate that $user is a valid number
    if (filter_var($user, FILTER_VALIDATE_INT) === false) {
        echo json_encode(['error' => 'Invalid user parameter.']);
        exit;
    }

    // Fetch data from tbl_monthly_dues
    try {
        // $query = "SELECT distinct h.History_ID, h.Dues_ID, h.Resident_ID, m.LongName as Month, h.Date, h.Amount 
        // FROM tbl_history h , tbl_months m
        // WHERE Resident_ID = :residentId";
       // $query = "SELECT distinct h.History_ID, h.Dues_ID, h.Resident_ID, m.LongName as Month, h.Date, h.Amount
       // FROM tbl_history h JOIN tbl_months m ON h.Month = m.LongName WHERE Resident_ID = :residentId";

    //    $query = "SELECT 
    //             COALESCE(h.History_ID, '') as History_ID,
    //             COALESCE(h.Date, '') as Payment_Date,
    //             COALESCE(h.Dues_ID, '')  AS Dues_ID,
    //             COALESCE(md.Year, h.Year) AS Year, 
    //             COALESCE(h.Resident_ID, '') AS Resident_ID, 
    //             COALESCE(m.LongName, '')  AS Month, 
    //             COALESCE(md.Monthly_Due_with_Light, '')  as Amount_Due, 
    //             COALESCE(h.Amount, '')  AS Amount_Paid
    //         FROM tbl_months m
    //         LEFT JOIN tbl_history h 
    //             ON h.Month = m.LongName AND h.Resident_ID = :residentId
    //         LEFT JOIN tbl_maintenance_mdues md 
    //             ON md.Month_ID = m.Month_ID
    //         WHERE h.Resident_ID = :residentId OR h.Resident_ID IS NULL
    //         AND md.Year = 2025";

        $query = "SELECT DISTINCT
    COALESCE(h.History_ID, '') AS History_ID,
    COALESCE(h.Date, '') AS Payment_Date,
    COALESCE(md.Dues_ID, '') AS Dues_ID,
    COALESCE(md.Year, 2025) AS Year,
    8 AS Resident_ID,
    m.LongName AS Month,
    COALESCE(md.Amount, 0) AS Amount_Due,
    COALESCE(h.Amount, 0) AS Amount_Paid
FROM tbl_months m
LEFT JOIN tbl_history h 
    ON h.Month = m.LongName AND h.Resident_ID = :residentId AND h.Year = 2025
LEFT JOIN tbl_monthly_dues md 
    ON md.Resident = 8 AND md.Year = 2025
ORDER BY COALESCE(md.Year, 2025), m.Month_ID;";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':residentId', $user, PDO::PARAM_INT);
        //To get year parameter - 01/1/2025
        //$stmt->bindParam(':year', $year, PDO::PARAM_INT);
        $stmt->execute();
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the result as JSON
        echo json_encode($history ? $history : []);
    } catch (PDOException $e) {
        // Handle database query errors
        echo json_encode(['error' => 'Database query failed.', 'details' => $e->getMessage()]);
    }
} else {
    // Handle the case where 'user' is not provided
    echo json_encode(['error' => 'No user parameter provided.']);
}
?>