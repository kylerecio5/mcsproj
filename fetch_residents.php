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

// Fetch residents from the database
$sql = "SELECT * FROM tbl_residents";
$result = $conn->query($sql);

$residents = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = [
            "id" => $row["Resident_ID"], // Resident ID
            "first_name" => $row["F_name"], // First name
            "middle_name" => $row["M_name"], // Middle name
            "last_name" => $row["L_name"], // Last name
            "name" => $row["F_name"] . " " . $row["M_name"] . " " . $row["L_name"], // Full name
            "residentcode" => $row["residentcode"], // residentcode
            "PhoneNum" => $row["PhoneNum"], // Phone number
            "member_type" => $row["MemberType"], // Member type
            "sex" => $row["Sex"], // Sex
            "age" => $row["Age"], // Age
            "block" => $row["Block"], // Block
            "lot" => $row["Lot"], // Lot
            "street" => $row["Street"], // Street
            "membership" => $row["Membership"] // Membership status
        ];
    }
}

echo json_encode($residents);
$conn->close();
?>
