<?php
// Database connection
$host = "localhost";
$dbname = "tms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch residents with dues information
$sql = "SELECT * FROM tbl_monthly_dues"; 
$sql = "SELECT * FROM tbl_residents"; 
$result = $conn->query($sql);

$dues = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dues[] = [
            "name" => $row["Resident"],
            "phone_number" => $row["PhoneNum"],
            "member_type" => $row["MemberType"],
            "street_light" => $row["StreetLight"],
            "amount" => $row["Amount"]
        ];
    }
}

echo json_encode($dues);
$conn->close();
?>
